<form action="" method="POST">
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Enter Amount</label>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="amount" required placeholder="Enter Amount">
                                        </div>
                                    </div>
                                   
                                    <div class="form-group row">
                                        <label class="col-form-label col-md-2">Select Mode</label>
                                        <div class="col-md-2">
                                            <input type="hidden" name="ahkweb_payment" value="ahkwebsolutions" >
                                            <input type="hidden" class="form-control" name="email" value="<?php echo $suserdata['email'] ?>">
                                            <input type="hidden" class="form-control" name="name" value="<?php echo $suserdata['name'] ?>">
                                            <input type="hidden" class="form-control" name="phone" value="<?php echo $suserdata['mobile'] ?>">
                                            <select name="pay_type" class="form-control">
                                                <option value="upi" selected >UPI Gateway</option>
                                                <!--<option value="paytm">PAYTM Gateway</option>-->
                                            </select>
                                        </div>
                                       
                                        <button class="btn btn-primary btn-sm" style="width:120px;" type="submit">Submit</button>
                                    </div>
                                    
           
                                    
                                </form>
<?php 
include('../includes/session.php');
include('../includes/config.php');
include('templates/'.$admin_template.'/wallet.php');

if(isset($_POST['ahkweb_payment']) && $_POST['ahkweb_payment'] == "ahkwebsolutions"){
    $ORDER_ID =  "AHK" . rand(000000000,999999999);
    $amount = mysqli_real_escape_string($ahk_conn,$_POST['amount']);
    $date = date("d-m-Y");
    $ins = mysqli_query($ahk_conn,"INSERT INTO `payments`(`username`, `order_id`, `amount`,`status`,`txn_date`) VALUES ('$susername','$ORDER_ID','$amount','pending','$date')");

    if($ins){
                                $name = $_POST['name'];
                                $email = $_POST['email'];
                                $phone = $_POST['phone'];
                                
                                $key = "KEY";    // you can get your key from https://merchant.upigateway.com/user/api_credentials
                            
                                 $content = json_encode(array(
                            	 	"key"=> $key,
                            	 	"client_txn_id"=> "$ORDER_ID", // order id or your own transaction id
                            	 	"amount"=> "$amount", 
                            	 	"p_info"=> "Wallet balance",
                            	 	"customer_name"=> "$name", // customer name
                            	 	"customer_email"=> "$email", // customer email
                            	 	"customer_mobile"=> "$phone", // customer mobile number
                            	 	"redirect_url"=> "https://lostpan.in/admin/payment_handler.php", // redirect url after payment, with ?client_txn_id=&txn_id=
                            	 	"udf1"=> "$susername", // udf1, udf2 and udf3 are used to save other order related data, like customer id etc.
                            	 	"udf2"=> "user defined field 2",
                            	 	"udf3"=> "user defined field 3",
                            	 ));
                            	 $url = "https://merchant.upigateway.com/api/create_order";
                            	 $curl = curl_init($url);
                            	 curl_setopt($curl, CURLOPT_HEADER, false);
                            	 curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                            	 curl_setopt($curl, CURLOPT_HTTPHEADER,
                            	 		array("Content-type: application/json"));
                            	 curl_setopt($curl, CURLOPT_POST, true);
                            	 curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                            	 $json_response = curl_exec($curl);
                            	 $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                            	 if ( $status != 200 ) {
                            	 	// You can handle Error yourself.
                            	 	die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
                            	 }
                            	 curl_close($curl);
                            	 $response = json_decode($json_response, true);
                            	 if($response["status"] == true){
                            	 	// Method 1
                            	 	// redirect to payment page of UPI
                            	 	header("Location: ".$response["data"]["payment_url"]);
                            	 	die();
                            	 	// Method 2
                            	 	// echo "<script>window.location.href='".$response["data"]["payment_url"]."'</script>";
                            	 	// die();
                            	 }else{
                            	 	echo $response['msg'];
                            	 }
        
    }
}
if(isset($_POST['success']) && $_POST['success'] == "true"){
    ?>
    <script>
        $(function(){
            Swal.fire(
                'Payment Added Successfully',
                'Your Payment Added!',
                'success'
            )
        })
    </script>
    <?php
}
if(isset($_POST['failed']) && $_POST['failed'] == "true"){
    ?>
    <script>
        $(function(){
            Swal.fire(
                'Payment Added Failed',
                'if Payment Deduct Then Contact us!',
                'error'
            )
        })
    </script>
    <?php
}
