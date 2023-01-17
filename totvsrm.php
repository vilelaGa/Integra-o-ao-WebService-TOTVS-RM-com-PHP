<?php

/*
CRIADO POR GABRIEL VILELA
Acesse: https://github.com/vilelaGa
*/


require_once "vendor/autoload.php";

use App\Dbconnect\Dbconnect;


//Query do meu SGBD para popular o xml (Crie do seu jeito)
$con = (new Dbconnect())->select_webservice("VALIDACAO = 1 AND MIGRACAO IS NULL")
    ->fetchAll(PDO::FETCH_ASSOC);


foreach ($con as $linha) {
    @$xml .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tot="http://www.totvs.com/">';
    $xml .= '<soapenv:Header/>';
    $xml .= '<soapenv:Body>';
    $xml .= '<tot:SaveRecord>';
    $xml .= '<tot:DataServerName>Nome_do_seu_data_server</tot:DataServerName>';
    $xml .= '<tot:XML>';
    $xml .= '<SATIVIDADEALUNO>';
    $xml .= '<CODCOLIGADA>' . $linha['CODCOLIGADA'] . '</CODCOLIGADA>';
    $xml .= '<IDATIVIDADE>0</IDATIVIDADE>';
    $xml .= '<IDHABILITACAOFILIAL>' . $linha['IDHABILITACAOFILIAL'] . '</IDHABILITACAOFILIAL>';
    $xml .= '<RA>' . $linha['RA'] . '</RA>';
    $xml .= '<IDPERLET>' . $linha['IDPERLET'] . '</IDPERLET>';
    $xml .= '<IDOFERTA></IDOFERTA>';
    $xml .= '<CODCOMPONENTE>' . $linha['CODCOMPONENTE'] . '</CODCOMPONENTE>';
    $xml .= '<CODMODALIDADE>' . $linha['CODMODALIDADE'] . '</CODMODALIDADE>';
    $xml .= '<CARGAHORARIA>' . str_replace('.0000', '', $linha['CARGAHORARIA']) . '</CARGAHORARIA>';
    $xml .= '<CREDITOS></CREDITOS>';
    $xml .= '<DESCRICAO>' . $linha['DESCRICAO'] . '</DESCRICAO>';
    $xml .= '<DATA></DATA>';
    $xml .= '<OBSERVACAO>' . $linha['OBSERVACAO'] . '</OBSERVACAO>';
    $xml .= '<CUMPRIUATIVIDADE>' . $linha['CUMPRIUATIVIDADE'] . '</CUMPRIUATIVIDADE>';
    $xml .= '<DOCUMENTACAOENTREGUE>' . $linha['DOCUMENTACAOENTREGUE'] . '</DOCUMENTACAOENTREGUE>';
    $xml .= '<INSCRICAOCONFIRMADA>' . $linha['INSCRICAOCONFIRMADA'] . '</INSCRICAOCONFIRMADA>';
    $xml .= '<CODSTATUS></CODSTATUS>';
    $xml .= '<DATAINICIO>' . $linha['DATAINICIO'] . '</DATAINICIO>';
    $xml .= '<DATAFIM>' . $linha['DATAFIM'] . '</DATAFIM>';
    $xml .= '<NOMECURSO></NOMECURSO>';
    $xml .= '<NOMEHABILITACAO></NOMEHABILITACAO>';
    $xml .= '<NOMEGRADE></NOMEGRADE>';
    $xml .= '<NOMETURNO></NOMETURNO>';
    $xml .= '<CODPERLET>' . $linha['CODPERLET'] . '</CODPERLET>';
    $xml .= '<DESCCOMPONENTE></DESCCOMPONENTE>';
    $xml .= '<DESCMODALIDADE></DESCMODALIDADE>';
    $xml .= '<DESCOFERTADA></DESCOFERTADA>';
    $xml .= '<CODINST></CODINST>';
    $xml .= '<LOCAL></LOCAL>';
    $xml .= '<CONVENIO></CONVENIO>';
    $xml .= '<CARGAHORARIAATV>' . str_replace('.0000', '', $linha['CARGAHORARIA']) . '</CARGAHORARIAATV>';
    $xml .= '<CODTIPOPART></CODTIPOPART>';
    $xml .= '<CODFILIAL>' . $linha['CODFILIAL'] . '</CODFILIAL>';
    $xml .= '<CODPREDIO></CODPREDIO>';
    $xml .= '<CODBLOCO></CODBLOCO>';
    $xml .= '<CODSALA></CODSALA>';
    $xml .= '<CODUSUARIO></CODUSUARIO>';
    $xml .= '<CODPESSOA></CODPESSOA>';
    $xml .= '<NOMEALUNO></NOMEALUNO>';
    $xml .= '<CARGAHOR></CARGAHOR>';
    $xml .= '<CARGAHORGRADE></CARGAHORGRADE>';
    $xml .= '<CONTEUDO></CONTEUDO>';
    $xml .= '<CREDITO></CREDITO>';
    $xml .= '<DTFINAL></DTFINAL>';
    $xml .= '<DTFINALINSC></DTFINALINSC>';
    $xml .= '<DTINICIAL></DTINICIAL>';
    $xml .= '<DTINICIALINSC></DTINICIALINSC>';
    $xml .= '</SATIVIDADEALUNO>';
    $xml .= '</tot:XML>';
    $xml .= '<tot:Contexto>?</tot:Contexto>';
    $xml .= '</tot:SaveRecord>';
    $xml .= '</soapenv:Body>';
    $xml .= '</soapenv:Envelope>';
    $xml .= 'GoHorse';
}


//Explode para tranformar em array cada xml
$explode_array = explode('GoHorse', $xml);

//Remove o último índice do array que e sempre vazio
array_pop($explode_array);


//Se quiser ver a estrutura do xml descomente o for

// for ($i = 0; $i < count($con); $i++) {
//     $arquivo = fopen("for$i" . ".xml", 'w+');
//     $escrever = fwrite($arquivo, $explode_array[$i]);
//     fclose($arquivo);
// }


//Coloque a url do seu dataserver RM
$WsdlRM = "http://url_do_seu_data_server";


//Array com as credencias para acessar o serviço do seu dataserver RM
$soapParams = [
    'login' => 'seu_codigo',
    'password' => 'sua_senha',
    'authentication' => SOAP_AUTHENTICATION_BASIC,
    'trace' => 1,
    'exceptions' => 0
];


try {

    //Função que abre a conexão soap no php 
    $client = new SoapClient($WsdlRM, $soapParams);

    echo '<h3>Conectou com sucesso</h3> <br>';

    //Esse for e foreach são adaptado pra o meu problema, então analize oque você quer fazer
    for ($i = 0; $i < count($con); $i++) {

        foreach ($con as $linha) {

            extract($linha);

            //Esse array contém os parâmetros que devem ser inseridos no method do dataserver (Obrigatório)
            $params = ['DataServerName' => 'Nome_do_seu_data_server', 'XML' => $explode_array[$i], 'Contexto' => "CODCOLIGADA=$CODCOLIGADA;CODFILIAL=$CODFILIAL;CODTIPOCURSO=$CODTIPOCURSO;CODSISTEMA=S"];
        }

        //Executa o method dentro do webservice (SaveRecord = Salvar detro do RM)
        $result = $client->SaveRecord($params);

        print_r('<b>Resposta Data Server:</b> ' . $client->__getLastResponse());
    }

    // print_r($client->__getTypes());

} catch (SoapFault $e) {
    echo "Error!";
    echo $e->getMessage();
    echo 'Last response: ' . $client->__getLastResponse();
}
