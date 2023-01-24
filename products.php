<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Products</title>
	<script src="dist/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="dist/jquery-confirm.min.css">
	<script src="dist/jquery-confirm.min.js"></script>

	<script type="text/javascript">

		function search(data){
			var barcodedata = data.value;

			alert(asd);

          	$.ajax(
                	{
                    url:"query.php",
                    method:"POST",
                    data: { barcode: barcodedata },
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "xml",
                    success:function(xml)
                    {
                    	alert('asd');
                        $(xml).find('output').each(function()
                        {
                        	
                            var message = $(this).attr('Message');
                            var barcode = $(this).attr('Barcode');
                            var productName = $(this).attr('ProductName');
                            var warehouseQty = $(this).attr('WarehouseQty');
                            var storeQty = $(this).attr('StoreQty');


                            $('#TxtResBarcode').val(barcode);
                            $('#TxtProductName').val(productName);
                            $('#TxtWarehouseQty').val(warehouseQty);
                            $('#TxtStoreQty').val(storeQty);


                      
                        });
                     },
                    error: function (e)
                    {
                    	
                        $.alert(
                        {theme: 'modern',
                        content:'Failed to store information due to error',
                        title:'', 
                        buttons:{
                            Ok:{
                            text:'Ok',
                            btnClass: 'btn-red'
                        }}});
                    }
                });
		}


		$(document).ready(function() 
            {

            	event.preventDefault();
                
                $("#product-form").submit(function(event)
                {          
                	event.preventDefault();      
                    /* stop form from submitting normally 
                    event.preventDefault();
          			/*var form_data = new FormData(this);
          			alert("asd");
                    search(form_data);
                	
                });
            }); 
	</script>

</head>
<body>
	<h3>Product List</h3>
	<form method="post" id="product-form">
		<div>
			<label for="TxtBarcode">Barcode</label>
			<input type="text" id="TxtBarcode" onchange="search(this)" name="TxtBarcode" placeholder="Barcode">
		</div>
		<div>
			<input type="submit" name="submit" value="Submit">
		</div>
	</form>
		<br>
		<div>
			<label for="TxtResBarcode">Barcode</label>
			<input type="text" id="TxtResBarcode" name="TxtResBarcode" placeholder="barcode" readonly>
			<label for="TxtProductName">Product Name</label>
			<input type="text" id="TxtProductName" name="TxtProductName" readonly>
			<label for="TxtWarehouseQty">Warehouse Qty</label>
			<input type="text" id="TxtWarehouseQty" name="TxtWarehouseQty">
			<label for="TxtStoreQty">Store Qty</label>
			<input type="text" id="TxtStoreQty" name="TxtStoreQty">
		</div>
		
</body>
</html>
