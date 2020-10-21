<?php
require_once "../conexao.php";
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
       case 'PUT':

              break;
       case 'POST':
              if(isset($_POST['searchUser'])){
                     searchUser();
              }
              break;
       case 'GET':
              if (isset($_GET['authMatricula'])) {
                     auth();
              } elseif (isset($_GET['getByid'])) {
                     get();
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


function auth()
{
       try {
              $Conexao = Connection::getConnection();
              $query = $Conexao->prepare("SELECT * FROM usuario WHERE matricula_user = :mat");
              $query->bindValue(":mat", $_GET["authMatricula"]);
              $query->execute();
              echo json_encode($query->fetchObject());
       } catch (Exception $e) {
              echo json_encode('{"err": "' . $e . '"}');
       }
}
function get()
{
       try {
              $Conexao = Connection::getConnection();
              $query = $Conexao->prepare("SELECT * FROM usuario WHERE id_user = :id");
              $query->bindValue(":id", $_GET["getByid"]);
              $query->execute();
              echo json_encode($query->fetchObject());
       } catch (Exception $e) {
              echo json_encode('{"err": "' . $e . '"}');
       }
}
function getAll()
{
       try {
              $Conexao = Connection::getConnection();
              $query = $Conexao->prepare("SELECT * FROM usuario");
              $query->execute();
              echo json_encode($query->fetchAll(PDO::FETCH_OBJ));
       } catch (Exception $e) {
              echo json_encode('{"err": "' . $e . '"}');
       }
}

function searchUser()
{
       try {
              $Conexao = Connection::getConnection();
              $query = $Conexao->prepare("SELECT * FROM usuario WHERE nome_user LIKE '%".$_POST['searchUser']."%' OR matricula_user LIKE '%".$_POST['searchUser']."%' ");
              $query->execute();
              echo json_encode($query->fetchAll());
       } catch (Exception $e) {
              echo json_encode('{"err": "' . $e . '"}');
              
       }
}
