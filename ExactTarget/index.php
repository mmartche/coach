<?php
require('ExactTargetSoap.php');

function exactEmail($ds_email, $mensagem, $parametro = "", $subjectt = null) {
    //ini_set("display_errors", 1);
    $wsdl = 'https://webservice.s6.exacttarget.com/etframework.wsdl';
    $client = new ExactTargetSoap($wsdl, array('trace'=>1));
    $client->username = 'adminexact';
    $client->password = 'Esfera@2013';

    $ts = new ExactTarget_TriggeredSend();
    $tsd = new ExactTarget_TriggeredSendDefinition();
    $tsd->CustomerKey = $parametro;

    $sub = new ExactTarget_Subscriber();
    $sub->EmailAddress = $ds_email;
    $sub->SubscriberKey = $ds_email;

    $html =  new ExactTarget_Attribute();
    $html->Name = "CUPOM";
    $html->Value = $mensagem;

    if ($subjectt != null) {
        $subject =  new ExactTarget_Attribute();
        $subject->Name = "Nome";
        $subject->Value = $subjectt;
        $sub->Attributes = array($html, $subject);
    } else {
        $sub->Attributes = array($html);
    }

    $ts->Subscribers = array();
    $ts->Subscribers = $sub;
    $ts->TriggeredSendDefinition = $tsd;

    $object = new SoapVar($ts, SOAP_ENC_OBJECT, 'TriggeredSend', "http://exacttarget.com/wsdl/partnerAPI");
    $request = new ExactTarget_CreateRequest();
    $request->Options = NULL;
    $request->Objects = array($object);

    $retorno = $client->Create($request);
    return $retorno;
}

$db_host = "o2-db-ativos-servicos.cgqle4cw3o4s.us-east-1.rds.amazonaws.com";
$db_username = "servico_esferabr";
$db_password = "ser6*hpqa#vb5=ke";
$db_name = "o2-coach";

// $db_host = "localhost";
// $db_username = "root";
// $db_password = "";
// $db_name = "ativocoach";

$db_conn = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($db_conn->connect_error) {
    print_r("Send Email failed: " . $db_conn->connect_error);
} else {
    $db_conn->set_charset("utf8");
    $sql_list_email = "SELECT * FROM coach_ativocoach_email WHERE date_send < date_added";
    print_r("Send  trying: ");
    // "INSERT INTO " . DB_PREFIX . "ativocoach_email (`subject`, `message`, `sender_from`, `sender_name`, `sender_to`, `header`, `parameters`) VALUES ('".$filter_data['subject']."', '".$filter_data['message']."', '".$filter_data['sender_from']."', '".$filter_data['sender_name']."', '".$filter_data['sender_to']."', '".$filter_data['header']."', '".$filter_data['parameters']."')";
    $result_list_email = $db_conn->query($sql_list_email);
    foreach ($result_list_email as $result_list => $key) {
        // var_dump($key["sender_to"], $key["message"], "VerificaremailCoach", $key["subject"]);
        $varvar = exactEmail($key["sender_to"], $key["message"], "VerificaremailCoach", $key["subject"]);
        var_dump($varvar->Results);
        if($varvar->Results->StatusCode == "OK") {
            $db_conn->query("UPDATE coach_ativocoach_email SET exact_return = '".$varvar->Results->StatusMessage."', date_send = now() WHERE email_id = '".$key['email_id']."'");
        }
        print_r("<hr>");
    }
// var_dump($result_list_email);
}

if (!empty($_POST["ds_email"])) {
    print_r("<hr>");
    var_dump($_POST);
    print_r("<hr>");
    $varvar = exactEmail($_POST["ds_email"], $_POST["mensagem"], $_POST["parametro"], $_POST["parametro"]);
    var_dump($varvar->Results);
}
?>

<form method="post">
    <input type="text" name="ds_email" placeholder="ds_email" />
    <input type="text" name="mensagem" placeholder="mensagem" />
    <input type="text" name="parametro" placeholder="parametro" />
    <input type="submit" name="vaitesta" />
</form>