<?php
        $connectionString = "DefaultEndpointsProtocol=https;AccountName=".getenv('blobff').";AccountKey=".getenv('9SkV9J8qyevowLNw6rXH1eOSbfKnRYohlvOhEwWUHJZMiZP4AiD24smx/xkLRyLBg3+8c5PdjYzcemAP6Pf1EQ==');

        //$connectionString = 'DefaultEndpointsProtocol=http;AccountName=blobff;AccountKey=9SkV9J8qyevowLNw6rXH1eOSbfKnRYohlvOhEwWUHJZMiZP4AiD24smx/xkLRyLBg3+8c5PdjYzcemAP6Pf1EQ==';
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($connectionString);
        $fileToUpload = fopen($_FILES['inputImage']['tmp_name'].'', "r");
        
        if (!isset($_GET["Cleanup"])) {
            // Create container options object.
            $createContainerOptions = new CreateContainerOptions();
            // Set public access policy. Possible values are
            // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
            // CONTAINER_AND_BLOBS:
            // Specifies full public read access for container and blob data.
            // proxys can enumerate blobs within the container via anonymous
            // request, but cannot enumerate containers within the storage account.
            //
            // BLOBS_ONLY:
            // Specifies public read access for blobs. Blob data within this
            // container can be read via anonymous request, but container data is not
            // available. proxys cannot enumerate blobs within the container via
            // anonymous request.
            // If this value is not specified in the request, container data is
            // private to the account owner.
            $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
            // Set container metadata.
            $createContainerOptions->addMetaData("key1", "value1");
            $createContainerOptions->addMetaData("key2", "value2");
            $containerName = "blockblobs".generateRandomString();
            try {
                // Create container.
                $blobClient->createContainer($containerName, $createContainerOptions);
                // Getting local file so that we can upload it to Azure
                $myfile = fopen($fileToUpload, "w") or die("Unable to open file!");
                fclose($myfile);
        
                # Upload file as a block blob
                echo "Uploading BlockBlob: ".PHP_EOL;
                echo $fileToUpload;
                echo "<br />";
        
                $content = fopen($fileToUpload, "r");
                //Upload blob
                $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
                // List blobs.
                $listBlobsOptions = new ListBlobsOptions();
                $listBlobsOptions->setPrefix("HelloWorld");
                echo "These are the blobs present in the container: ";
                do{
                    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
                    foreach ($result->getBlobs() as $blob)
                    {
                        echo $blob->getName().": ".$blob->getUrl()."<br />";
                    }
                    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
                echo "<br />";
                // Get blob.
                echo "This is the content of the blob uploaded: ";
                $blob = $blobClient->getBlob($containerName, $fileToUpload);
                fpassthru($blob->getContentStream());
                echo "<br />";
            }
            catch(ServiceException $e){
                // Handle exception based on error codes and messages.
                // Error codes and messages are here:
                // http://msdn.microsoft.com/library/azure/dd179439.aspx
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }
            catch(InvalidArgumentTypeException $e){
                // Handle exception based on error codes and messages.
                // Error codes and messages are here:
                // http://msdn.microsoft.com/library/azure/dd179439.aspx
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }
        } 
        else{
            try{
                // Delete container.
                echo "Deleting Container".PHP_EOL;
                echo $_GET["containerName"].PHP_EOL;
                echo "<br />";
                $blobClient->deleteContainer($_GET["containerName"]);
            }
            catch(ServiceException $e){
            // Handle exception based on error codes and messages.
            // Error codes and messages are here:
            // http://msdn.microsoft.com/library/azure/dd179439.aspx
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo $code.": ".$error_message."<br />";
            }
        }
        
        
        // **********************************************
        // *** Update or verify the following values. ***
        // **********************************************
 
        // Replace <Subscription Key> with your valid subscription key.
        var subscriptionKey = "ef9e7d8394e24d87a1a08487ce5eca5b";
 
        // You must use the same Azure region in your REST API method as you used to
        // get your subscription keys. For example, if you got your subscription keys
        // from the West US region, replace "westcentralus" in the URL
        // below with "westus".
        //
        // Free trial subscription keys are generated in the "westus" region.
        // If you use a free trial subscription key, you shouldn't need to change
        // this region.
        var uriBase =
            "https://computer-visionn.cognitiveservices.azure.com/vision/v2.0/analyze";
 
        // Request parameters.
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
 
        // Display the image.
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
 
        // Make the REST API call.
        $.ajax({
            url: uriBase + "?" + $.param(params),
 
            // Request headers.
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
 
            // Request body.
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            // Show formatted JSON on webpage.
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            // Display error message.
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });

>