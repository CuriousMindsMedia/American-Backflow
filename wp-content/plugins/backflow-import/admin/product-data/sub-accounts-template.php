<?php /* Template Name: Sub Accounts Template */ ?>

<?php get_header(); 

$DDIQuoteInit = new DDIQuoteSystem();
$userSubAccounts = $DDIQuoteInit->listUserSubAccounts();
$userSubAccount = $_COOKIE["bf-user-account"];
$setUserCookie = false;
if(isset($userSubAccount)){
    $setUserCookie = true;
}

if($setUserCookie){
  $subAccountData = $DDIQuoteInit->getUserSubAccountById($userSubAccount)[0];
}

?>

<div class="container">
<div class="row">
<div class="col-sm-12">
<div class="sub-account-log">
<div class="alert alert-info">

<?php if($setUserCookie && $_GET["logout-sub-account"] != "true"){
    echo "You are logged in with the sub account: <strong>" . $subAccountData["account_title"] . "</strong> <a style='float:right' href='#' class='sub-account-logout'>Log out of Sub Account</a>";
} else {
    echo "You are not logged into a sub account";
}
?>
</div>
</div>
</div>
<div class="col-sm-6">
<div id="create-sub-account">
<h3>Create a Sub Account</h3>
<form id="create-sub-account-form">

<label> <span>Sub Account Name: </span> <input type="text" class="sub-account-name" placeholder="Sub Account Name"> </label>
<button type="submit" class="bf-btn bf-btn-blue bf-btn-sm uppercase">Create Sub Account</button>

</form>

</div>
</div>
<div class="col-sm-6">

<h3>Sub Accounts</h3>
<div class="sub-accounts-list">
<ul>
<?php 
    foreach($userSubAccounts as $userSubAccount){
        echo "<li><span>".$userSubAccount["account_title"]."</span> <a href='?login-as=".$userSubAccount["id"]."' data-id='".$userSubAccount["id"]."' class='set-user-sub-account bf-btn bf-btn-blue bf-btn-sm uppercase'>Login in as ".$userSubAccount["account_title"]." </a></li>";
    }

?>
</ul>


</div>
</div>


</div>
</div>

<style>
#create-sub-account-form label span{
       float: left;
    width: 100%;
    font-size: 21px;
    margin-bottom: 16px; 
}
#create-sub-account-form{
    float:left;
    width:100%;
    position:relative;
    margin-bottom:36px;
}
#create-sub-account-form label{
    float:left;
    width:100%;
    position:relative;
}
.sub-accounts-list{
    float:left;
    width:100%;
    margin-bottom:78px;
}
.sub-accounts-list ul{
    list-style:none;
    float:left;
    width:100%;
    margin:0px;
    padding:0px;
}
.sub-accounts-list ul li span{
    float:left;
    line-height:40px;
    font-weight:600;
    font-size:18px;
}
.sub-accounts-list ul li{
    float:left;
    width:100%;
    padding:8px 14px;
    border-bottom:solid 1px rgba(0,0,0,.15);
}
.sub-accounts-list ul li a{
    float:right;
}
</style>
<?php get_footer(); ?>


