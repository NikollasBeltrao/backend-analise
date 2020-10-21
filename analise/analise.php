<?php

require_once "../conexao.php";
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'PUT':

        break;
    case 'POST':
        if (isset($_POST['searchUser'])) {
            searchUser();
        }
        break;
    case 'GET':
        if (isset($_GET['getByid'])) {
            getAll();
        } elseif (isset($_GET['getAnalises'])) {
            getAnalises();
        } elseif (isset($_GET['getAll'])) {
            getAll();
        }

        break;
    case 'DELETE':

        break;
    default:
        echo '{"error": "Método inválido"}';
        break;
}
//LEFT JOIN hedonica as h on h.amostra = am.id_amostra LEFT JOIN compra as c on c.amostra = am.id_amostra
function getAnalises()
{
    $Conexao = Connection::getConnection();
    $query1 = $Conexao->query("SELECT an.*, at.*, u.id_user, u.nome_user, u.matricula_user, u.permissoes    
    FROM analise AS an JOIN analisetipos AS at ON an.id_analise = at.fk_analise JOIN usuario AS u ON u.id_user = an.fk_user");
    $analisesOBJ = json_encode($query1->fetchAll(PDO::FETCH_OBJ));
    if (isset($_GET['getAnalises'])) {
        echo $analisesOBJ;
    } else {

        return $analisesOBJ;
    }
}
function getAnalise()
{
    $Conexao = Connection::getConnection();
    $query1 = $Conexao->query("SELECT an.*, at.*, u.id_user, u.nome_user, u.matricula_user, u.permissoes    
    FROM analise AS an JOIN analisetipos AS at ON an.id_analise = at.fk_analise JOIN usuario AS u ON u.id_user = an.fk_user 
    WHERE an.id_analise = " . $_GET['getByid']);
    $analisesOBJ = json_encode($query1->fetchAll(PDO::FETCH_OBJ));
    
    if (!isset($_GET['getByid'])) {
        echo $analisesOBJ;
    } else {
        return $analisesOBJ;
    }
}
function getAll()
{
    try {
        $Conexao = Connection::getConnection();
        $analisesOBJ = [];
        if (isset($_GET['getByid'])) {
            $analisesOBJ =  getAnalise();
        } else {
            $analisesOBJ = getAnalises();
        }
        $analisesOBJ = json_decode($analisesOBJ, true);

        $i = 0;
        foreach ($analisesOBJ as $value) {            
            $query2 = $Conexao->query("SELECT * FROM amostra AS a Where a.fk_analise = " . $value['id_analise']);
            $j = 0;
            $amostras = json_encode($query2->fetchAll(PDO::FETCH_OBJ));
            $amostras = json_decode($amostras, true);
            //pegar as amsotras          
            foreach ($amostras as $value2) {

                $analisesOBJ[$i]["amostras"][] = $value2;
                $query31 = $Conexao->query("SELECT * FROM hedonica AS h JOIN ficha AS f ON h.fk_ficha = f.id_ficha Where h.amostra = " . $value2['id_amostra']);
                $hedonica = json_encode($query31->fetchAll(PDO::FETCH_OBJ));
                $hedonica = json_decode($hedonica, true);
                $query32 = $Conexao->query("SELECT * FROM compra AS c JOIN ficha AS f ON c.fk_ficha = f.id_ficha Where c.amostra = " . $value2['id_amostra']);
                $compra = json_encode($query32->fetchAll(PDO::FETCH_OBJ));
                $compra = json_decode($compra, true);
                $analisesOBJ[$i]["amostras"][$j]["hedonica"] = $hedonica;
                $analisesOBJ[$i]["amostras"][$j]["compra"] = $compra;
                $j = $j + 1;
            }
            $i = $i + 1;
        }
        echo json_encode($analisesOBJ);
    } catch (Exception $e) {
        echo json_encode('{"err": "' . $e . '"}');
    }
}

if (isset($_FILES['file'])) {
    echo '{"asd": "asdas"}';
    // Recupera os dados dos campos
    $foto = $_FILES["file"];

    // Se a foto estiver sido selecionada
    if (!empty($foto["name"])) {

        // Largura máxima em pixels
        $largura = 150;
        // Altura máxima em pixels
        $altura = 180;
        // Tamanho máximo do arquivo em bytes
        $tamanho = 1000;
        $error = array();
        // Verifica se o arquivo é uma imagem
        //if(!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $foto["type"])){
        //  $error[1] = "Isso não é uma imagem.";
        //  } 

        // Pega as dimensões da imagem
        $dimensoes = getimagesize($foto["tmp_name"]);

        // Verifica se a largura da imagem é maior que a largura permitida
        //if($dimensoes[0] > $largura) {
        //    $error[2] = "A largura da imagem não deve ultrapassar ".$largura." pixels";
        //}
        // Verifica se a altura da imagem é maior que a altura permitida
        //if($dimensoes[1] > $altura) {
        //    $error[3] = "Altura da imagem não deve ultrapassar ".$altura." pixels";
        //}

        // Verifica se o tamanho da imagem é maior que o tamanho permitido
        //if($foto["size"] > $tamanho) {
        //        $error[4] = "A imagem deve ter no máximo ".$tamanho." bytes";
        //}
        // Se não houver nenhum erro


        if (count($error) == 0) {
            try {
                // Pega extensão da imagem
                preg_match("/\.(gif|bmp|png|jpg|jpeg){1}$/i", $foto["name"], $ext);
                // Gera um nome único para a imagem
                $nome_imagem = md5(uniqid(time())) . "." . $ext[1];
                // Caminho de onde ficará a imagem
                $caminho_imagem = "imgsamostras/" . $nome_imagem;
                // Faz o upload da imagem para seu respectivo caminho
                move_uploaded_file($foto["tmp_name"], $caminho_imagem);
                echo '{"resposta": "Criado com sucesso"}';
            } catch (Exception $e) {
                echo  '{"error": "falha ao criar o arquivo"}';
            }
        }

        // Se houver mensagens de erro, exibe-as
        if (count($error) != 0) {
            foreach ($error as $erro) {
                echo  '{"err": "asdasd"}';
            }
        }
    }
}
