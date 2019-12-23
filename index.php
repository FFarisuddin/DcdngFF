<?php
    require_once 'vendor/autoload.php';
    require_once "./random_string.php";

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

    $connectionString = 'DefaultEndpointsProtocol=http;AccountName=blobff;AccountKey=9SkV9J8qyevowLNw6rXH1eOSbfKnRYohlvOhEwWUHJZMiZP4AiD24smx/xkLRyLBg3+8c5PdjYzcemAP6Pf1EQ==';
    $containerName = "images";
    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    if (isset($_POST['submit'])) {
        $fileToUpload = strtolower($_FILES["photo"]["name"]);
        $content = fopen($_FILES["photo"]["tmp_name"], "r");
        // echo fread($content, filesize($fileToUpload));
        $blobClient->createBlockBlob($containerName, $fileToUpload, $content);
        header("Location: index.php");
    }
    $listBlobsOptions = new ListBlobsOptions();
    $listBlobsOptions->setPrefix("");
    $result = $blobClient->listBlobs($containerName, $listBlobsOptions);
?>
<!DOCTYPE html>
<html>
<head>
    <title>AGIT</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>
<body>
    <h1>Cognitive Service x Blob Storage</h1>

    <form action="index.php" method="post" enctype="multipart/form-data">
        Image to analyze: <input type="file" name="photo" accept=".jpeg,.jpg,.png" />
        <input type="submit" name="Submit" value="Uploadd" />
    </form> 

    <br><br>
    
    <div id="wrapper" style="width:1020px; display:table;">
        <div id="jsonOutput" style="width:600px; display:table-cell;">
            Response:
            <br><br>
            <textarea id="responseTextArea" class="UIInput" style="width:580px; height:400px;"></textarea>
        </div>
    
        <div id="imageDiv" style="width:420px; display:table-cell;">
            Source image:
            <br><br>
            <img id="sourceImage" width="400" />
        </div>
    </div>
</body>
</html>
