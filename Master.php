<?php 
if(session_id() ==="")
session_start();
require_once('DBConnection.php');
/**
 * Login Registration Class
 */
Class Master extends DBConnection{
    function __construct(){
        parent::__construct();
    }
    function __destruct(){
        parent::__destruct();
    }
    function save_settings(){
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['wallet_management'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            $user_id = $_SESSION['user_id'];
            $columns = [];
            $values = [];
            foreach($_POST as $k => $v){
                if(!is_array($_POST[$k]) && !in_array($k, ['formToken'])){
                    $columns[] = $k;
                    $values[] = $v;
                }
            }
            if(empty($columns) && empty($values)){
               $resp['status'] = 'failed';
               $resp['msg'] = "No data has been sent.";
            }else{
                foreach($columns as $k => $v){
                    $setting_id = "";
                    $check = $this->query("SELECT setting_id FROM `settings` where `user_id` = '{$user_id}' and `name` = '{$v}'");
                    $settingsData = $check->fetchArray();
                    if(!empty($settingsData)){
                        $setting_id = $settingsData['setting_id'];
                    }
                    if(!empty($setting_id)){
                        $sql = "UPDATE `settings` set `value` = '{$values[$k]}' where `setting_id` = '{$setting_id}'";
                    }else{
                        $sql = "INSERT INTO `settings` (`user_id`, `name`, `value`) VALUES ('{$user_id}', '{$v}', '{$values[$k]}')";
                    }
                    $qry = $this->query($sql);
                    if(!$qry){
                        $resp['status'] = 'failed';
                        $resp['msg'] = "Error: ".$this->lastErrorMsg();
                        break;
                    }
                }
                $resp['status'] = 'success';
                $resp['msg'] = "Wallet Data has been updated successfully.";
            }
        }
        return json_encode($resp);
    }

    function save_expense(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['expense-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            if(empty($expense_id)){
                $sql = "INSERT INTO `expenses` (`user_id`, `name`, `amount`) VALUES ('{$user_id}', '{$name}', '{$amount}')";
            }else{
                $sql = "UPDATE `expenses` set `name` = '{$name}', `amount` = '{$amount}' where `expense_id` = '{$expense_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($expense_id))
                $resp['msg'] = 'New Expense has been addedd successfully';
                else
                $resp['msg'] = 'Expense Data has been updated successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function get_expense(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['expenses'];
        if(!isset($token) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "SELECT `expense_id`, `user_id`, `name`, `amount` FROM `expenses` where `expense_id` = '{$id}'";
            $qry = $this->query($sql);
            $data = $qry->fetchArray();
            $resp['status'] = 'success';
            $resp['data'] = $data;
        }
        return json_encode($resp);
    }
    function delete_expense(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['expenses'];
        if(!isset($token) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `expenses` where `expense_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The expense data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function save_earning(){
        if(!isset($_POST['user_id']))
        $_POST['user_id'] = $_SESSION['user_id'];
        foreach($_POST as $k => $v){
            if(!in_array($k, ['formToken']) && !is_array($_POST[$k]) && !is_numeric($v)){
                $_POST[$k] = $this->escapeString($v);
            }
        }
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['earning-form'];
        if(!isset($formToken) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Form Token is invalid.";
        }else{
            if(empty($earning_id)){
                $sql = "INSERT INTO `earnings` (`user_id`, `name`, `amount`) VALUES ('{$user_id}', '{$name}', '{$amount}')";
            }else{
                $sql = "UPDATE `earnings` set `name` = '{$name}', `amount` = '{$amount}' where `earning_id` = '{$earning_id}'";
            }
            $qry = $this->query($sql);
            if($qry){
                $resp['status'] = 'success';
                if(empty($earning_id))
                $resp['msg'] = 'New earning has been addedd successfully';
                else
                $resp['msg'] = 'earning Data has been updated successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Error:'. $this->lastErrorMsg(). ", SQL: {$sql}";
            }
        }
        return json_encode($resp);
    }
    function get_earning(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['earnings'];
        if(!isset($token) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "SELECT `earning_id`, `user_id`, `name`, `amount` FROM `earnings` where `earning_id` = '{$id}'";
            $qry = $this->query($sql);
            $data = $qry->fetchArray();
            $resp['status'] = 'success';
            $resp['data'] = $data;
        }
        return json_encode($resp);
    }
    function delete_earning(){
        extract($_POST);
        $allowedToken = $_SESSION['formToken']['earnings'];
        if(!isset($token) || (isset($formToken) && $formToken != $allowedToken)){
            $resp['status'] = 'failed';
            $resp['msg'] = "Security Check: Token is invalid.";
        }else{
            $sql = "DELETE FROM `earnings` where `earning_id` = '{$id}'";
            $delete = $this->query($sql);
            if($delete){
                $resp['status'] = 'success';
                $resp['msg'] = 'The earning data has been deleted successfully';
                $_SESSION['message']['success'] = $resp['msg'];
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = $this->lastErrorMsg();
            }
        }
        return json_encode($resp);
    }
    function total_earnings(){
        $sql = "SELECT SUM(`amount`) from `earnings` where `user_id` = '{$_SESSION['user_id']}' ";
        $earnings = $this->querySingle($sql);
        return $earnings ?? 0;
    }
    function total_expenses(){
        $sql = "SELECT SUM(`amount`) from `expenses` where `user_id` = '{$_SESSION['user_id']}' ";
        $expenses = $this->querySingle($sql);
        return $expenses ?? 0;
    }
    function get_baseAmount(){
        $sql = "SELECT `value` from `settings` where `user_id` = '{$_SESSION['user_id']}' and `name` = 'startingBalance' ";
        $startingBalance = $this->querySingle($sql);
        return $startingBalance ?? 0;
    }
    function get_total_wallet(){
        $expenses = $this->total_expenses();
        $earnings = $this->total_earnings();
        $baseWallet = $this->get_baseAmount();
        $total = ($baseWallet + $earnings) - $expenses;
        return $total;
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$master = new Master();
switch($a){
    case 'save_settings':
        echo $master->save_settings();
    break;
    case 'save_expense':
        echo $master->save_expense();
    break;
    case 'get_expense':
        echo $master->get_expense();
    break;
    case 'delete_expense':
        echo $master->delete_expense();
    break;
    case 'save_earning':
        echo $master->save_earning();
    break;
    case 'get_earning':
        echo $master->get_earning();
    break;
    case 'delete_earning':
        echo $master->delete_earning();
    break;
    default:
    // default action here
    break;
}