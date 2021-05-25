<?php
$conn = new mysqli("host","user","password","database");

// Check connection
if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}
$id = null;

if(isset($_GET['id'])){
    $id=$_GET['id'];
}
?>
    
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="ZXing for JS">
    <title>Barcode Scanner</title>
    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null" href="https://unpkg.com/normalize.css@8.0.0/normalize.css">
    <link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null" href="https://unpkg.com/milligram@1.3.0/dist/milligram.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
     <style>
        table, th, td 
        {
            font: 17px Calibri;
            border: solid 1px #DDD;
            border-collapse: collapse;
            padding: 2px 3px;
            text-align: center;
            margin: 10px 0;
        }
        th {
            font-weight:bold;
        }
    </style>
</head>

<body>
    <main class="wrapper" style="padding-top:2em">
        <section class="container" id="demo-content">
            <h1 class="title">Scan barcode</h1>
            <div style="display:none">
                <a class="button" id="startButton" >Start</a>
                <a class="button" id="resetButton" >Reset</a>
            </div>
            <div>
                <video id="video" width="600" height="400" style="border: 1px solid gray"></video>
            </div>
            <div id="sourceSelectPanel" style="display:none">
                <label for="sourceSelect">Change video source:</label>
                <select id="sourceSelect" style="max-width:400px">
                </select>
            </div>
            <label>Result:</label>
            <pre><code id="result"></code></pre>
            <?php 
                $sql = "SELECT id, name, type, price FROM product WHERE id='$id'";
                $result = $conn->query($sql);
                if ($id != null) {
                // output data of each row
                while($row = $result->fetch_assoc()) { ?>
                <table id='table-to-refresh' cellspacing='0'><tr><th>Product code</th><th>Product name</th><th>Product type</th><th>Price</th></tr><tbody id="rows">
                <tr><td><?php echo $row["id"]; ?></td><td> <?php echo $row["name"]; ?></td><td>
                <?php echo $row["type"]; ?></td><td> <?php echo $row["price"]; ?></td></tr></tbody>
               </table><?php
                }} else { echo "0 result"; }
                $conn->close();
            ?>
        </section>
        <footer class="footer">
        </footer>
    </main>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
    <script type="text/javascript">
    
        window.addEventListener('load', function () {
            let selectedDeviceId;
            const codeReader = new ZXing.BrowserBarcodeReader()
            console.log('ZXing code reader initialized')
            codeReader.getVideoInputDevices()
                .then((videoInputDevices) => {
                    const sourceSelect = document.getElementById('sourceSelect')
                    selectedDeviceId = videoInputDevices[0].deviceId
                    if (videoInputDevices.length > 1) {
                        videoInputDevices.forEach((element) => {
                            const sourceOption = document.createElement('option')
                            sourceOption.text = element.label
                            sourceOption.value = element.deviceId
                            sourceSelect.appendChild(sourceOption)
                        })

                        sourceSelect.onchange = () => {
                            selectedDeviceId = sourceSelect.value;
                        }

                        const sourceSelectPanel = document.getElementById('sourceSelectPanel')
                        sourceSelectPanel.style.display = 'block'
                    }

                    document.getElementById('startButton').addEventListener('click', () => {
                        codeReader.decodeOnceFromVideoDevice(selectedDeviceId, 'video').then((result) => {
                            console.log(result.text);
                            if(result.text != document.getElementById('result').innerHTML){
                                console.log('changed');
                                window.location.href = "?id="+result.text;
                                document.getElementById('result').innerHTML = result.text;
                            }
                            document.getElementById('result').innerHTML = result.text;
                            var code = document.getElementById('result').innerHTML;
                            
                            
                        }).catch((err) => {
                            console.error(err)
                            document.getElementById('result').textContent = err
                        });
                       
                        
                        console.log(`Started continous decode from camera with id ${selectedDeviceId}`)
                    })

                    document.getElementById('resetButton').addEventListener('click', () => {
                        document.getElementById('result').textContent = '';
                        codeReader.reset();
                        console.log('Reset.')
                    })
                    
                     setTimeout(function clickfunction(){
                         document.getElementById('startButton').click();
                    },1000)
                    clickfunction();
                    
                    function addData(data) {
                    var data;
                    var id = data;
                    var newRow = document.createElement("tr");
                    var newCell = document.createElement("td");
                    newCell.innerHTML = id;
                    newRow.append(newCell);
                    document.getElementById("rows").appendChild(newRow);
                    }
                })
                .catch((err) => {
                    console.error(err)
                })
        })
    </script>
</body>
</html>