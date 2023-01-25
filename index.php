<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Products</title>
	<script src="dist/jquery-3.6.0.min.js"></script>
	<link rel="stylesheet" href="dist/jquery-confirm.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
	<script src="dist/jquery-confirm.min.js"></script>
	<script type="text/javascript">

    var TempBtnValue;

    function btnValue(valu){
        TempBtnValue = valu;
    }

    function saveRecords(form_data)
            {   
                $.ajax(
                { 
                    url:"saveData.php",
                    method:"POST",
                    data:form_data, 
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "xml",
                    success:function(xml)
                    {
                        $(xml).find('output').each(function()
                        {
                            var message = $(this).attr('Message');
                            var error = $(this).attr('Error');

                            if(error == "1"){
                                //Display Alert Box
                                $.alert(
                                {theme: 'modern',
                                content: message,
                                title:'', 
                                buttons:{
                                    Ok:{
                                    text:'Ok',
                                    btnClass: 'btn-red'
                                }}});
                            }else{
                                $.confirm({
                                    title: '',
                                    theme: 'modern',
                                    content: message,
                                    buttons: {
                                        somethingElse: {
                                            text: 'Ok',
                                            btnClass: 'btn-green',
                                            keys: ['enter'],
                                            action: function(){
                                                location.reload();
                                            }
                                        }
                                    }
                                });

                            }
                            
                        });
                     },
                    error: function (e)
                    {
                        //Display Alert Box
                        $.alert(
                        {theme: 'modern',
                        content:'Failed to save information due to error',
                        title:'', 
                        buttons:{
                            Ok:{
                            text:'Ok',
                            btnClass: 'btn-red'
                        }}});
                    }
                });
            }

            function deleteRecords(form_data)
            {   
                $.ajax(
                { 
                    url:"deleteData.php",
                    method:"POST",
                    data:form_data, 
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: "xml",
                    success:function(xml)
                    {
                        $(xml).find('output').each(function()
                        {
                            var message = $(this).attr('Message');
                            var error = $(this).attr('Error');

                            if(error == "1"){
                                //Display Alert Box
                                $.alert(
                                {theme: 'modern',
                                content: message,
                                title:'', 
                                buttons:{
                                    Ok:{
                                    text:'Ok',
                                    btnClass: 'btn-red'
                                }}});
                            }else{
                                $.confirm({
                                    title: '',
                                    theme: 'modern',
                                    content: message,
                                    buttons: {
                                        somethingElse: {
                                            text: 'Ok',
                                            btnClass: 'btn-green',
                                            keys: ['enter'],
                                            action: function(){
                                                location.reload();
                                            }
                                        }
                                    }
                                });
                            }
                            
                        });
                     },
                    error: function (e)
                    {
                        //Display Alert Box
                        $.alert(
                        {theme: 'modern',
                        content:'Failed to delete information due to error',
                        title:'', 
                        buttons:{
                            Ok:{
                            text:'Ok',
                            btnClass: 'btn-red'
                        }}});
                    }
                });
            }


		function search(form_data){
            var temp = document.getElementById('TxtBarcode').value;
                    
            var form_data = new FormData();
            form_data.append("temp", temp);

            $.ajax(
                    { 
                        url:"query.php",
                        method:"POST",
                        data:form_data, 
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: "xml",
                        success:function(xml)
                        {
                            $(xml).find('output').each(function()
                            {
                                var message = $(this).attr('Message');
                                var error = $(this).attr('Error');
                                var ProductName = $(this).attr('ProductName');
                                var WarehouseQty = $(this).attr('WarehouseQty');
                                var StoreQty = $(this).attr('StoreQty');
                            
                                if(error == "1"){
                                        $('#TxtBarcode').val('');
                                        $('#TxtResBarcode').val('');
                                        $('#TxtProductName').val('');
                                        $('#TxtWarehouseQty').val('');
                                        $('#TxtStoreQty').val('');
                                }else{
                                        $('#TxtBarcode').val(document.getElementById('TxtBarcode').value);
                                        $('#TxtResBarcode').val(document.getElementById('TxtBarcode').value);
                                        $('#TxtProductName').val(ProductName);
                                        $('#TxtWarehouseQty').val(WarehouseQty);
                                        $('#TxtStoreQty').val(StoreQty);

                                        document.getElementById("BtnSave").removeAttribute("disabled");
                                        document.getElementById("BtnDelete").removeAttribute("disabled");
                                        document.getElementById("TxtWarehouseQty").removeAttribute("readonly");
                                        document.getElementById("TxtStoreQty").removeAttribute("readonly");

                                    
                                }      
                            });
                        },  
                        error: function (e)
                        {
                            //Display Alert Box
                            $.alert(
                            {theme: 'modern',
                            content:'Failed to fetch information due to error',
                            title:'', 
                            useBootstrap: false,
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
                $("#product-form").submit(function(event)
                {
                    event.preventDefault();          
          			var form_data = new FormData(this);

                    if(TempBtnValue == "save"){
                        saveRecords(form_data);
                    }else{
                        deleteRecords(form_data);
                    }
                	
                });
            }); 
	</script>

</head>
<body>
	<h3>Product List</h3>
	<form action="#" method="post" id="product-form">
		<div class="input_field">
			<label for="TxtBarcode" class="label">Barcode</label>
			<input type="text" id="TxtBarcode" onchange="search(this)" name="TxtBarcode">
		</div>
		<br>
		<div class="FourInfo">
            <div class="input_field">
                <label for="TxtResBarcode" class="label">Barcode</label><br/>
                <input type="text" id="TxtResBarcode" name="TxtResBarcode" readonly>
            </div>
            <div class="input_field">
                <label for="TxtProductName" class="label">Product Name</label>
                <input type="text" id="TxtProductName" name="TxtProductName" readonly> 
            </div>
            <div class="input_field">
                <label for="TxtWarehouseQty" class="label">Warehouse Quantity</label>
                <input type="text" id="TxtWarehouseQty" name="TxtWarehouseQty" readonly>
            </div>
            <div class="input_field">
                <label for="TxtStoreQty" class="label">Store Quantity</label>
                <input type="text" id="TxtStoreQty" name="TxtStoreQty" readonly>
            </div>
		</div>
        <div class="TwoButton">
            <div class="submit">
                <button type="submit" id="BtnSave" class="frmButton" name="BtnSave" onclick="btnValue('save')" disabled><svg class="icon" xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/><g id="SVGRepo_iconCarrier"> <path d="M8 20H6C4.89543 20 4 19.1046 4 18V6C4 4.89543 4.89543 4 6 4H9M8 20V14C8 13.4477 8.44772 13 9 13H15C15.5523 13 16 13.4477 16 14V20M8 20H16M16 20H18C19.1046 20 20 19.1046 20 18V8.82843C20 8.29799 19.7893 7.78929 19.4142 7.41421L16.5858 4.58579C16.2107 4.21071 15.702 4 15.1716 4H15M15 4V7C15 7.55228 14.5523 8 14 8H10C9.44772 8 9 7.55228 9 7V4M15 4H9" stroke="#000000" stroke-width="5%" stroke-linecap="round" stroke-linejoin="round"/> </g>
				</svg></button>
            </div>
            <div class="submit">
                <button type="submit" id="BtnDelete" class="frmDelete" name="BtnDelete" onclick="btnValue('delete')" disabled><svg xmlns="http://www.w3.org/2000/svg" class="icon" width="26px" height="30px" viewBox="0 0 1024 1024" class="icon" version="1.1" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"/><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="2.048"/><g id="SVGRepo_iconCarrier"><path d="M960 160h-291.2a160 160 0 0 0-313.6 0H64a32 32 0 0 0 0 64h896a32 32 0 0 0 0-64zM512 96a96 96 0 0 1 90.24 64h-180.48A96 96 0 0 1 512 96zM844.16 290.56a32 32 0 0 0-34.88 6.72A32 32 0 0 0 800 320a32 32 0 1 0 64 0 33.6 33.6 0 0 0-9.28-22.72 32 32 0 0 0-10.56-6.72zM832 416a32 32 0 0 0-32 32v96a32 32 0 0 0 64 0v-96a32 32 0 0 0-32-32zM832 640a32 32 0 0 0-32 32v224a32 32 0 0 1-32 32H256a32 32 0 0 1-32-32V320a32 32 0 0 0-64 0v576a96 96 0 0 0 96 96h512a96 96 0 0 0 96-96v-224a32 32 0 0 0-32-32z" fill="#000000"/><path d="M384 768V352a32 32 0 0 0-64 0v416a32 32 0 0 0 64 0zM544 768V352a32 32 0 0 0-64 0v416a32 32 0 0 0 64 0zM704 768V352a32 32 0 0 0-64 0v416a32 32 0 0 0 64 0z" fill="#000000"/></g></svg></button>
            </div>
        </div>
    </form>
</body>
</html>
