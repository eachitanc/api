<?php
session_start();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app = new \Slim\App;
$conexion = '../datos/config/conexion.php';
//GET Consultar municipio por ID de dpto
$app->get('/res/municipios/{id}', function (Request $request, Response $response) {
    $id_dpto = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_municipios WHERE id_departamento = '$id_dpto' ORDER BY nom_municipio";
        $rs = $cmd->query($sql);
        $municipios = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if (!empty($municipios)) {
        echo json_encode($municipios);
    } else {
        echo json_encode('0');
    }
});
//GET Consultar dpto
$app->get('/res/dptos', function (Request $request, Response $response) {
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_departamento ORDER BY nombre_dpto";
        $rs = $cmd->query($sql);
        $dpto = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if (!empty($dpto)) {
        echo json_encode($dpto);
    } else {
        echo json_encode('0');
    }
});
//GET Consultar tipo de documento
$app->get('/res/tipo/identificacion', function (Request $request, Response $response) {
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_tipos_documento";
        $rs = $cmd->query($sql);
        $tipodoc = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if (!empty($tipodoc)) {
        echo json_encode($tipodoc);
    } else {
        echo json_encode('0');
    }
});
//Consultar actividades economicas
$app->get('/res/lista/actividades', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_actividades_economicas";
        $rs = $cmd->query($sql);
        $actividades = $rs->fetchAll();
        if (!empty($actividades)) {
            echo json_encode($actividades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Consultar responsabilidades economicas
$app->get('/res/lista/responsabilidades', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_responsabilidades_tributarias";
        $rs = $cmd->query($sql);
        $responsabilidad = $rs->fetchAll();
        if (!empty($responsabilidad)) {
            echo json_encode($responsabilidad);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
$app->get('/res/login/{id}', function (Request $request, Response $response) {
    $ids = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM seg_terceros
                WHERE cc_nit  = '$ids'";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if (!empty($tercero)) {
        echo json_encode($tercero);
    } else {
        echo json_encode('0');
    }
});
$app->get('/res/lista/{id}', function (Request $request, Response $response) {
    $ids = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM
                    seg_terceros
                INNER JOIN seg_pais 
                    ON (seg_terceros.pais = seg_pais.id_pais)
                INNER JOIN seg_departamento 
                    ON (seg_departamento.id_pais = seg_pais.id_pais) AND (seg_terceros.departamento = seg_departamento.id_dpto)
                INNER JOIN seg_municipios 
                    ON (seg_municipios.id_departamento = seg_departamento.id_dpto) AND (seg_terceros.municipio = seg_municipios.id_municipio)
                WHERE cc_nit  IN ($ids) ORDER BY apellido1,apellido2, nombre1,nombre2,razon_social";
        $rs = $cmd->query($sql);
        $terceros = $rs->fetchAll();
        if (!empty($terceros)) {
            echo json_encode($terceros);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//GET Datos UP por ID
$app->get('/res/lista/datos_up/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    id_tercero, tipo_doc, cc_nit, apellido1, apellido2, nombre1, nombre2, razon_social, pais, departamento, municipio, direccion, telefono, correo, genero, fec_nacimiento
                FROM
                    seg_terceros
                WHERE cc_nit = '$id'";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        if (!empty($tercero)) {
            echo json_encode($tercero);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//GET Datos por id de tercero
$app->get('/res/datos/id/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    *
                FROM
                    seg_terceros
                WHERE id_tercero IN ($id)";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetchAll();
        if (!empty($tercero)) {
            echo json_encode($tercero);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//POST Nuevo tercero
$app->post('/res/nuevo', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $genero = $data['slcGenero'];
    $fecNacimiento = date('Y-m-d', strtotime($data['datFecNacimiento']));
    $tipodoc = $data['slcTipoDocEmp'];
    $cc_nit = $data['txtCCempleado'];
    $nomb1 = $data['txtNomb1Emp'];
    $nomb2 = $data['txtNomb2Emp'];
    $ape1 = $data['txtApe1Emp'];
    $ape2 = $data['txtApe2Emp'];
    $razonsoc = $data['txtRazonSocial'];
    $pais = $data['slcPaisEmp'];
    $dpto = $data['slcDptoEmp'];
    $municip = $data['slcMunicipioEmp'];
    $dir = $data['txtDireccion'];
    $mail = $data['mailEmp'];
    $tel = $data['txtTelEmp'];
    $iduser = $data['id_user'];
    $tipouser = 'user';
    $docreg = $data['nit_emp'];
    $pass = $data['pass'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO seg_terceros(genero, fec_nacimiento, tipo_doc, cc_nit, nombre1, nombre2, apellido1, apellido2, razon_social, pais, departamento, municipio, direccion, correo, telefono, id_user_reg, password, tipo_user_reg, fec_reg, doc_reg) "
            . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $genero, PDO::PARAM_STR);
        $sql->bindParam(2, $fecNacimiento, PDO::PARAM_STR);
        $sql->bindParam(3, $tipodoc, PDO::PARAM_INT);
        $sql->bindParam(4, $cc_nit, PDO::PARAM_STR);
        $sql->bindParam(5, $nomb1, PDO::PARAM_STR);
        $sql->bindParam(6, $nomb2, PDO::PARAM_STR);
        $sql->bindParam(7, $ape1, PDO::PARAM_STR);
        $sql->bindParam(8, $ape2, PDO::PARAM_STR);
        $sql->bindParam(9, $razonsoc, PDO::PARAM_STR);
        $sql->bindParam(10, $pais, PDO::PARAM_INT);
        $sql->bindParam(11, $dpto, PDO::PARAM_INT);
        $sql->bindParam(12, $municip, PDO::PARAM_INT);
        $sql->bindParam(13, $dir, PDO::PARAM_STR);
        $sql->bindParam(14, $mail, PDO::PARAM_STR);
        $sql->bindParam(15, $tel, PDO::PARAM_STR);
        $sql->bindParam(16, $iduser, PDO::PARAM_INT);
        $sql->bindParam(17, $pass, PDO::PARAM_STR);
        $sql->bindParam(18, $tipouser, PDO::PARAM_STR);
        $sql->bindValue(19, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(20, $docreg, PDO::PARAM_STR);
        $sql->execute();
        $id_api = $cmd->lastInsertId();
        if ($id_api > 0) {
            echo json_encode($id_api);
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT modificar contraseña tercero
$app->put('/res/modificar/pass', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $newpass = $data['newpass'];
    $idter = $data['idter'];
    $iduser = $data['iduser'];
    $tipuser = $data['tipuser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_terceros SET password = ? WHERE id_tercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $newpass, PDO::PARAM_STR);
        $sql->bindParam(2, $idter, PDO::PARAM_INT);
        $sql->execute();
        $cambio = $sql->rowCount();
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($cambio > 0) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_terceros SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_tercero = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $idter, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    echo json_encode(1);
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
            } else {
                echo json_encode('No se ingresó ningún dato nuevo');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT Modificar tercero
$app->put('/res/modificar/tercero/{id}', function (Request $request, Response $response) {
    $idter =  $request->getAttribute('id');
    $data = json_decode(file_get_contents('php://input'), true);
    $genero = $data['slcGenero'];
    $fecNacimiento = date('Y-m-d', strtotime($data['datFecNacimiento']));
    $tipodoc = $data['slcTipoDocEmp'];
    $cc_nit = $data['txtCCempleado'];
    $nomb1 = $data['txtNomb1Emp'];
    $nomb2 = $data['txtNomb2Emp'];
    $ape1 = $data['txtApe1Emp'];
    $ape2 = $data['txtApe2Emp'];
    $razonsoc = $data['txtRazonSocial'];
    $pais = $data['slcPaisEmp'];
    $dpto = $data['slcDptoEmp'];
    $municip = $data['slcMunicipioEmp'];
    $dir = $data['txtDireccion'];
    $mail = $data['mailEmp'];
    $tel = $data['txtTelEmp'];
    $iduser =  $data['id_user'];
    $tipuser = $data['tipuser'];
    $nit_act =  $data['nit_emp'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_terceros SET genero = ?, fec_nacimiento = ?, tipo_doc = ?, cc_nit = ?, nombre1 = ?, nombre2 = ?, apellido1 = ?, apellido2 = ?, razon_social = ?, pais = ?, departamento = ?, municipio = ?, direccion = ?, correo = ?, telefono = ? WHERE id_tercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $genero, PDO::PARAM_STR);
        $sql->bindParam(2, $fecNacimiento, PDO::PARAM_STR);
        $sql->bindParam(3, $tipodoc, PDO::PARAM_INT);
        $sql->bindParam(4, $cc_nit, PDO::PARAM_STR);
        $sql->bindParam(5, $nomb1, PDO::PARAM_STR);
        $sql->bindParam(6, $nomb2, PDO::PARAM_STR);
        $sql->bindParam(7, $ape1, PDO::PARAM_STR);
        $sql->bindParam(8, $ape2, PDO::PARAM_STR);
        $sql->bindParam(9, $razonsoc, PDO::PARAM_STR);
        $sql->bindParam(10, $pais, PDO::PARAM_INT);
        $sql->bindParam(11, $dpto, PDO::PARAM_INT);
        $sql->bindParam(12, $municip, PDO::PARAM_INT);
        $sql->bindParam(13, $dir, PDO::PARAM_STR);
        $sql->bindParam(14, $mail, PDO::PARAM_STR);
        $sql->bindParam(15, $tel, PDO::PARAM_STR);
        $sql->bindParam(16, $idter, PDO::PARAM_INT);
        $sql->execute();
        $cambio = $sql->rowCount();
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($cambio > 0) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_terceros SET  id_user_act = ?, tipo_user_act = ? , doc_act = ?, fec_act = ? WHERE id_tercero = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                $sql->bindParam(3, $nit_act, PDO::PARAM_STR);
                $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(5, $idter, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    echo json_encode(1);
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
            } else {
                echo json_encode('No se ingresó datos nuevos');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

//PUT Nuevo Resposabilidad Tercero
$app->PUT('/res/nuevo/responsabilidad', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $idt = $data["id_terero"];
    $id_resp_econ = $data["id_responsabilidad"];
    $iduser = $data["id_user"];
    $tipouser = $data["tipo_user"];
    $doc_reg = $data["nit_reg"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT  * FROM seg_responsabilidades_terceros
                WHERE id_tercero = '$idt' AND id_responsabilidad = '$id_resp_econ'";
        $rs = $cmd->query($sql);
        $resposabilidad = $rs->fetchAll();
        $cmd = null;
        if (!empty($resposabilidad)) {
            echo json_encode('Resposabilidad Económica ya se encuentra registrada');
        } else {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO seg_responsabilidades_terceros(id_tercero, id_responsabilidad, id_user_reg, tipo_user_reg, doc_reg, fec_reg) VALUES (?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idt, PDO::PARAM_INT);
                $sql->bindParam(2, $id_resp_econ, PDO::PARAM_INT);
                $sql->bindParam(3, $iduser, PDO::PARAM_INT);
                $sql->bindParam(4, $tipouser, PDO::PARAM_STR);
                $sql->bindParam(5, $doc_reg, PDO::PARAM_STR);
                $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($cmd->lastInsertId() > 0) {
                    echo json_encode(1);
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar estado responsabilidad
$app->PUT('/res/modificar/estado/responsabilidad', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $estado = $data["estado"];
    $idter = $data["idter"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_responsabilidades_terceros SET estado = ?, fec_act = ?, id_user_act = ?, tipo_user_act = ? WHERE id_resptercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $estado);
        $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(3, $iduser);
        $sql->bindParam(4, $tipuser);
        $sql->bindParam(5, $idter);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode($estado);
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar estado actividad
$app->PUT('/res/modificar/estado/actividad', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $estado = $data["estado"];
    $idter = $data["idter"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE seg_actividad_terceros SET estado = ?, fec_act = ?, id_user_act = ?, tipo_user_act = ? WHERE id_actvtercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $estado);
        $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(3, $iduser);
        $sql->bindParam(4, $tipuser);
        $sql->bindParam(5, $idter);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode($estado);
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Actualizar estado documento de soporte 
$app->PUT('/res/actualizar/estado_doc_soporte', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $estado = $data["estado"];
    $id_doc = $data["id_doc"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_docs_contrato` SET `estado` = ?, `fec_act` = ?, `id_user_act` = ?, `tipo_user_act` = ? WHERE `id_doc_c` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $estado);
        $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(3, $iduser);
        $sql->bindParam(4, $tipuser);
        $sql->bindParam(5, $id_doc);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode(1);
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT Nuevo Actividad Tercero
$app->put('/res/nuevo/actividad', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $idt = $data["id_tercero"];
    $id_actv_econ = $data["id_actividad"];
    $finic = $data["finic"];
    $iduser = $data["id_user"];
    $tipouser = $data["tipo_user"];
    $doc_reg = $data["nit_reg"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT  * FROM seg_actividad_terceros
            WHERE id_tercero = '$idt' AND id_actividad = '$id_actv_econ'";
        $rs = $cmd->query($sql);
        $actividad = $rs->fetchAll();
        $cmd = null;
        if (!empty($actividad)) {
            echo json_encode('Actividad Económica ya se encuentra registrada');
        } else {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO seg_actividad_terceros(id_tercero, id_actividad, fec_inicio, id_user_reg, tipo_user_reg, doc_reg, fec_reg) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idt, PDO::PARAM_INT);
                $sql->bindParam(2, $id_actv_econ, PDO::PARAM_INT);
                $sql->bindParam(3, $finic, PDO::PARAM_STR);
                $sql->bindParam(4, $iduser, PDO::PARAM_INT);
                $sql->bindParam(5, $tipouser, PDO::PARAM_STR);
                $sql->bindParam(6, $doc_reg, PDO::PARAM_STR);
                $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($cmd->lastInsertId() > 0) {
                    echo json_encode(1);
                } else {
                    $sql->errorInfo()[2];
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Detalles de tercero resposabilidad economica
$app->get('/res/lista/resp_econ/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    id_resptercero, seg_responsabilidades_terceros.id_responsabilidad, codigo,  descripcion, estado
                FROM
                    seg_responsabilidades_terceros
                INNER JOIN seg_responsabilidades_tributarias 
                    ON (seg_responsabilidades_terceros.id_responsabilidad = seg_responsabilidades_tributarias.id_responsabilidad)
                WHERE id_tercero = '$id'";
        $rs = $cmd->query($sql);
        $responsabilidades = $rs->fetchAll();
        if (!empty($responsabilidades)) {
            echo json_encode($responsabilidades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Detalles de tercero actividad economica
$app->get('/res/lista/actv_econ/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                id_actvtercero, id_tercero, codigo_ciiu, descripcion, fec_inicio, estado
            FROM
                seg_actividad_terceros
            INNER JOIN seg_actividades_economicas 
                ON (seg_actividad_terceros.id_actividad = seg_actividades_economicas.id_actividad)
            WHERE id_tercero = '$id'";
        $rs = $cmd->query($sql);
        $actvidades = $rs->fetchAll();
        if (!empty($actvidades)) {
            echo json_encode($actvidades);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT Nuevo Documento Tercero
$app->put('/res/nuevo/documento', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $idt = $data["idt"];
    $tipodoc = $data["tipodoc"];
    $fini = $data["fini"];
    $fvig = $data["fvig"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $nom_archivo = $data["nom_archivo"];
    $temporal = $data["temporal"];
    $temporal = base64_decode($temporal);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $ruta = '../../uploads/terceros/docs/' . $idt . '/';
        if (!file_exists($ruta)) {
            $ruta = mkdir('../../uploads/terceros/docs/' . $idt . '/', 0777, true);
            $ruta = '../../uploads/terceros/docs/' . $idt . '/';
        }
        $res = file_put_contents("$ruta/$nom_archivo", $temporal);
        if (false !== $res) {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO seg_docs_tercero(id_tercero, id_tipo_doc, fec_inicio, fec_vig, ruta_doc, nombre_doc, id_user_reg, tipo_user_reg, fec_reg)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $idt, PDO::PARAM_INT);
            $sql->bindParam(2, $tipodoc, PDO::PARAM_INT);
            $sql->bindParam(3, $fini, PDO::PARAM_STR);
            $sql->bindParam(4, $fvig, PDO::PARAM_STR);
            $sql->bindParam(5, $ruta, PDO::PARAM_STR);
            $sql->bindParam(6, $nom_archivo, PDO::PARAM_STR);
            $sql->bindParam(7, $iduser, PDO::PARAM_INT);
            $sql->bindParam(8, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(9, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//nuevo contrato
$app->put('/res/nuevo/contrato', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    include $GLOBALS['conexion'];
    $ccnit = $data["tercero"];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_tercero` FROM `seg_terceros` WHERE `cc_nit` = '$ccnit' ";
        $rs = $cmd->query($sql);
        $tercer = $rs->fetch();
        $doc_tercero = $tercer['id_tercero'];
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    $doc_tercero = isset($doc_tercero) ? $doc_tercero : exit('Error: tercero no encotrado');
    $id_contrato = $data["contrato"];
    $id_compra = $data["compra"];
    $nit_empres = $data["empresa"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $nom_archivo = $data["nom_archivo"];
    $temporal = $data["temporal"];
    $temporal = base64_decode($temporal);
    $r = [];
    try {
        $ruta = '../../uploads/terceros/contratos/' . $doc_tercero . '/';
        if (!file_exists($ruta)) {
            $ruta = mkdir('../../uploads/terceros/contratos/' . $doc_tercero . '/', 0777, true);
            $ruta = '../../uploads/terceros/contratos/' . $doc_tercero . '/';
        }
        $res = file_put_contents("$ruta/$nom_archivo", $temporal);
        if (false !== $res) {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_contratos`(`id_contrato`, `id_compra`, `nit_empresa`, `id_tercero`, `ruta_contrato`, `nombre_contrato`, `id_user_reg`, `tipo_user_reg`, `fec_reg`)
                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id_contrato, PDO::PARAM_INT);
            $sql->bindParam(2, $id_compra, PDO::PARAM_INT);
            $sql->bindParam(3, $nit_empres, PDO::PARAM_STR);
            $sql->bindParam(4, $doc_tercero, PDO::PARAM_INT);
            $sql->bindParam(5, $ruta, PDO::PARAM_STR);
            $sql->bindParam(6, $nom_archivo, PDO::PARAM_STR);
            $sql->bindParam(7, $iduser, PDO::PARAM_INT);
            $sql->bindParam(8, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(9, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            $id_crad = $cmd->lastInsertId();
            if ($id_crad > 0) {
                $r = [
                    'estado' => 1,
                    'response' => $id_crad,
                ];
                echo json_encode($r);
            } else {
                $r = [
                    'estado' => 0,
                    'response' => $sql->errorInfo()[2],
                ];
                echo json_encode($r);
            }
            $cdm = null;
        } else {
            $r = [
                'estado' => 0,
                'response' => 'No se pudo adjuntar el archivo',
            ];
            echo json_encode($r);
        }
    } catch (PDOException $e) {
        echo $res['response'] = ($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//nuevo contrado devuelto
$app->put('/res/nuevo/contrato_devolver', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    include $GLOBALS['conexion'];

    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    $id = $data["id_contrato_rec"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $nom_archivo = $data["nom_archivo"];
    $temporal = $data["temporal"];
    $temporal = base64_decode($temporal);
    try {
        $ruta = '../../uploads/terceros/contratos/' . $iduser . '/';
        if (!file_exists($ruta)) {
            $ruta = mkdir('../../uploads/terceros/contratos/' . $iduser . '/', 0777, true);
            $ruta = '../../uploads/terceros/contratos/' . $iduser . '/';
        }
        $res = file_put_contents("$ruta/$nom_archivo", $temporal);
        if (false !== $res) {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_contratos_enviados`(`id_c_rec`,`ruta_contrato`,`nombre_contrato`,`id_user_reg`,`tipo_user_reg`,`fec_reg`) VALUES (?, ?, ?, ?, ?, ?);";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id, PDO::PARAM_INT);
            $sql->bindParam(2, $ruta, PDO::PARAM_STR);
            $sql->bindParam(3, $nom_archivo, PDO::PARAM_STR);
            $sql->bindParam(4, $iduser, PDO::PARAM_INT);
            $sql->bindParam(5, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                try {
                    $estado = 2;
                    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
                    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                    $sql = "UPDATE `seg_contratos` SET `estado`= ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_c` = ?";
                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $estado, PDO::PARAM_INT);
                    $sql->bindParam(2, $iduser, PDO::PARAM_INT);
                    $sql->bindParam(3, $tipuser, PDO::PARAM_STR);
                    $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                    $sql->bindParam(5, $id, PDO::PARAM_INT);
                    $sql->execute();
                    if (!($sql->rowCount() > 0)) {
                        echo json_encode($sql->errorInfo()[2]);
                    } else {
                        echo json_encode(1);;
                    }
                    $cmd = null;
                } catch (PDOException $e) {
                    echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
                }
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//nuevo acta designación de supervisor
$app->put('/res/nuevo/documento/designacion_supervision', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    include $GLOBALS['conexion'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    $id_superv = $data["id_superv"];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_tercero`,`ruta`,`nombre` FROM `seg_supervisor_designado` WHERE `id_supervision` = '$id_superv' ";
        $rs = $cmd->query($sql);
        $tercero = $rs->fetch();
        $id_tercero = $tercero['id_tercero'];
        $ruta_do = $tercero['ruta'];
        $nom_doc = $tercero['nombre'];
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $rp = [];
    if ($ruta_do != '' && $nom_doc != '') {
        $rp = [
            'estado' => '0',
            'response' => 'Ya se registró una acta de Designación de supervisor',
        ];
    } else {
        $iduser = $data["iduser"];
        $tipuser = $data["tipuser"];
        $nom_archivo = $data["nom_archivo"];
        $temporal = $data["temporal"];
        $temporal = base64_decode($temporal);
        try {
            $ruta = '../../uploads/terceros/docs/' . $id_tercero . '/';
            if (!file_exists($ruta)) {
                $ruta = mkdir('../../uploads/terceros/docs/' . $id_tercero . '/', 0777, true);
                $ruta = '../../uploads/terceros/docs/' . $id_tercero . '/';
            }
            $res = file_put_contents("$ruta/$nom_archivo", $temporal);
            if (false !== $res) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE `seg_supervisor_designado` SET `ruta` = ?, `nombre` = ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_supervision` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $ruta, PDO::PARAM_STR);
                $sql->bindParam(2, $nom_archivo, PDO::PARAM_STR);
                $sql->bindParam(3, $iduser, PDO::PARAM_INT);
                $sql->bindParam(4, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(5, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(6, $id_superv, PDO::PARAM_INT);
                if (!($sql->execute())) {
                    $rp = [
                        'estado' => '0',
                        'response' => $sql->errorInfo()[2],
                    ];
                } else {
                    $rp = [
                        'estado' => '1',
                        'response' => '1',
                    ];
                }
            } else {
                $rp = [
                    'estado' => '0',
                    'response' => 'No se pudo adjuntar el archivo',
                ];
            }
        } catch (PDOException $e) {
            $rp['response'] = ($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    }
    echo json_encode($rp);
});
//Descargar documentos
$app->get('/res/descargar/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    *
                FROM
                    seg_tipo_docs_tercero";
        $rs = $cmd->query($sql);
        $tipo_docs = $rs->fetchAll();
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT 
                    id_docster, ruta_doc, nombre_doc
                FROM
                    seg_docs_tercero
                WHERE id_docster = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        $ruta = $docs['ruta_doc'] . $docs['nombre_doc'];
        $tipo = explode("_", $docs['nombre_doc']);
        $archivo = file_get_contents($ruta);
        $tip = $tipo[0];
        $key = array_search($tip, array_column($tipo_docs, 'id_doc'));
        if (false !== $key) {
            $res['tipo'] =  strtolower($tipo_docs[$key]['descripcion']);
        } else {
            $res['tipo'] = 'descarga';
        }
        $res['file'] = base64_encode($archivo);
        if (!empty($docs)) {
            echo json_encode($res);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Descargar documentos
$app->get('/res/descargar/contrato/{id}', function (Request $request, Response $response) {
    $datos = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_c`,`ruta_contrato`,`nombre_contrato` FROM `seg_contratos` WHERE `id_c`= '$datos'";
        $rs = $cmd->query($sql);
        $contrato = $rs->fetch();
        if ($contrato['ruta_contrato'] != '' && $contrato['nombre_contrato'] != '') {
            $ruta = $contrato['ruta_contrato'] . $contrato['nombre_contrato'];
            $archivo = file_get_contents($ruta);
            $res = base64_encode($archivo);
            echo json_encode($res);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Descargar documentos
$app->get('/res/descargar/docs/soporte/{id}', function (Request $request, Response $response) {
    $ruta = base64_decode($request->getAttribute('id'));
    if (file_exists($ruta)) {
        $archivo = file_get_contents($ruta);
        $res['file'] = base64_encode($archivo);
        echo json_encode($res);
    } else {
        echo json_encode('0');
    }
});
//IDs documentos de tercero
$app->get('/res/lista/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    seg_docs_tercero.id_tercero, seg_docs_tercero.id_tipo_doc, seg_docs_tercero.fec_vig, seg_terceros.cc_nit
                FROM
                    seg_docs_tercero
                INNER JOIN seg_terceros 
                        ON (seg_docs_tercero.id_tercero = seg_terceros.id_tercero)
                WHERE cc_nit = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetchAll();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

//Listar documenos por id de tercero
$app->get('/res/listar/docs/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    id_docster, id_tercero, id_tipo_doc, descripcion, fec_inicio, fec_vig, ruta_doc, nombre_doc
                FROM
                    seg_docs_tercero
                INNER JOIN seg_tipo_docs_tercero 
                    ON (seg_docs_tercero.id_tipo_doc = seg_tipo_docs_tercero.id_doc)
                WHERE id_Tercero = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetchAll();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//datos documento soporte de contrato
$app->get('/res/listar/detalles/docs_contrato/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_c_env`,`id_tipo_doc`,`otro_tipo`,`ruta_doc_c`,`nom_doc_c` FROM `seg_docs_contrato` WHERE `id_doc_c` = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Lista tipo documenos por id de tercero
$app->get('/res/listar/tipo/docs', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT * FROM seg_tipo_docs_tercero";
        $rs = $cmd->query($sql);
        $tipo = $rs->fetchAll();
        if (!empty($tipo)) {
            echo json_encode($tipo);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
$app->get('/res/listar/tipo/docs_contrato', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_doc_sop`,`descripcion` FROM `seg_tipo_doc_soporte`";
        $rs = $cmd->query($sql);
        $tipo = $rs->fetchAll();
        if (!empty($tipo)) {
            echo json_encode($tipo);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Lista datos documenos por id 
$app->get('/res/lista/documento/{id}', function (Request $request, Response $response) {
    $idDoc = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT *
                FROM
                    seg_docs_tercero
                INNER JOIN seg_tipo_docs_tercero 
                    ON (seg_docs_tercero.id_tipo_doc = seg_tipo_docs_tercero.id_doc)
                WHERE id_docster = '$idDoc'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('Sin datos');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT Modificar documentos
$app->put('/res/modificar/documento', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_ter = $data['id_ter'];
    $iddoc = $data['iddoc'];
    $idtipdoc = $data['idtipdoc'];
    $nom_archivo = $data['nom_archivo'];
    $nombre = $data['nombre'];
    $archivo = $data['archivo'];
    $fecini = $data['fecini'];
    $fecvig = $data['fecvig'];
    $iduser = $data['iduser'];
    $tipuser = $data['tipuser'];
    $temporal = $data['temporal'];
    $temporal = base64_decode($temporal);
    $ruta = '../../uploads/terceros/docs/' . $id_ter . '/';
    include $GLOBALS['conexion'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    if ($temporal !== '0') {
        unlink($ruta . $archivo);
        $nombre = $idtipdoc . '_' . date('YmdGis') . '_' . $nom_archivo;
        $nombre = strlen($nombre) >= 101 ? substr($nombre, 0, 100) : $nombre;
        $res = file_put_contents("$ruta/$nombre", $temporal);
        if (false !== $res) {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_docs_tercero SET id_tipo_doc = ?, fec_inicio  = ?, fec_vig = ?, nombre_doc = ? WHERE id_docster = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
                $sql->bindParam(2, $fecini, PDO::PARAM_STR);
                $sql->bindParam(3, $fecvig, PDO::PARAM_STR);
                $sql->bindParam(4, $nombre, PDO::PARAM_STR);
                $sql->bindParam(5, $iddoc, PDO::PARAM_INT);
                $sql->execute();
                $cambio = $sql->rowCount();
                if (!($sql->execute())) {
                    echo json_encode($sql->errorInfo()[2]);
                } else {
                    if ($cambio > 0) {
                        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                        $sql = "UPDATE seg_docs_tercero SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_docster = ?";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                        $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                        $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                        $sql->bindParam(4, $iddoc, PDO::PARAM_INT);
                        $sql->execute();
                        if ($sql->rowCount() > 0) {
                            echo json_encode(1);
                        } else {
                            echo json_encode($sql->errorInfo()[2]);
                        }
                    } else {
                        echo json_encode('No se ingresó ningún dato nuevo');
                    }
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } else {
        try {
            if ($nombre !== $archivo) {
                rename($ruta . $archivo, $ruta . $nombre);
            }
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "UPDATE seg_docs_tercero SET id_tipo_doc = ?, nombre_doc = ?, fec_inicio  = ?, fec_vig = ? WHERE id_docster = ?";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
            $sql->bindParam(2, $nombre, PDO::PARAM_STR);
            $sql->bindParam(3, $fecini, PDO::PARAM_STR);
            $sql->bindParam(4, $fecvig, PDO::PARAM_STR);
            $sql->bindParam(5, $iddoc, PDO::PARAM_INT);
            $sql->execute();
            $cambio = $sql->rowCount();
            if (!($sql->execute())) {
                echo json_encode($sql->errorInfo()[2]);
            } else {
                if ($cambio > 0) {
                    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                    $sql = "UPDATE seg_docs_tercero SET  id_user_act = ?, tipo_user_act = ? ,fec_act = ? WHERE id_docster = ?";
                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                    $sql->bindParam(2, $tipuser, PDO::PARAM_STR);
                    $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                    $sql->bindParam(4, $iddoc, PDO::PARAM_INT);
                    $sql->execute();
                    if ($sql->rowCount() > 0) {
                        echo json_encode(1);
                    } else {
                        echo json_encode($sql->errorInfo()[2]);
                    }
                } else {
                    echo json_encode('No se ingresó ningún dato nuevo');
                }
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    }
});
//PUT Modificar documento soporte de contrato
$app->put('/res/modificar/documento_soporte', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $iddoc = $data['iddoc'];
    $idtipdoc = $data['idtipdoc'];
    $otro = $idtipdoc == 99 ? $data['otro'] : '';
    $nom_archivo = $data['nom_archivo'];
    $iduser = $data['iduser'];
    $tipuser = $data['tipuser'];
    $temporal = $data['temporal'];
    $temporal = base64_decode($temporal);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    $estado_doc = 1;
    $res = [];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_doc_c`,`id_c_env`,`id_tipo_doc`,`otro_tipo`,`ruta_doc_c`,`nom_doc_c` FROM `seg_docs_contrato` WHERE `id_doc_c`= '$iddoc'";
        $rs = $cmd->query($sql);
        $doc_sop = $rs->fetch();
        if (!isset($doc_sop)) {
            $res = [
                'estado' => 0,
                'response' => 'Error: ' . $cdm->errorInfo()[2],
            ];
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($res['response'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        exit();
    }
    if ($idtipdoc == $doc_sop['id_tipo_doc'] && $temporal == '' && $otro == $doc_sop['otro_tipo']) {
        $res = [
            'estado' => 0,
            'response' => 'No se ha modificado ningún dato',
        ];
    } else if ($idtipdoc != $doc_sop['id_tipo_doc'] && $temporal == '') {
        $renom = explode('_', $doc_sop['nom_doc_c']);
        $newname = $idtipdoc;
        $ct = 0;
        foreach ($renom as $rn) {
            if ($ct > 0) {
                $newname .= '_' . $rn;
            }
            $ct++;
        }
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "UPDATE `seg_docs_contrato` SET `id_tipo_doc` = ?, `otro_tipo` = ?, `nom_doc_c` = ?, `id_user_act` = ?, `tipo_user_act` = ? , `fec_act` = ? WHERE `id_doc_c` = ?";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
            $sql->bindParam(2, $otro, PDO::PARAM_STR);
            $sql->bindParam(3, $newname, PDO::PARAM_STR);
            $sql->bindParam(4, $iduser, PDO::PARAM_INT);
            $sql->bindParam(5, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
            $sql->bindParam(7, $iddoc, PDO::PARAM_INT);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                rename($doc_sop['ruta_doc_c'] . $doc_sop['nom_doc_c'], $doc_sop['ruta_doc_c'] . $newname);
                $res = [
                    'estado' => 1,
                    'response' => 'Tipo de documento modificado correctamente',
                ];
            } else {
                $res = [
                    'estado' => 0,
                    'response' => 'Error: ' . $cdm->errorInfo()[2],
                ];
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($res['response'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            exit();
        }
    } else if ($temporal != '') {
        unlink($doc_sop['ruta_doc_c'] . $doc_sop['nom_doc_c']);
        $nombre = $idtipdoc . '_' . date('YmdGis') . '_' . $nom_archivo;
        $nombre = strlen($nombre) >= 101 ? substr($nombre, 0, 100) : $nombre;
        $res = file_put_contents($doc_sop['ruta_doc_c'] . $nombre, $temporal);
        if (false !== $res) {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE `seg_docs_contrato` SET `id_tipo_doc` = ?, `otro_tipo` = ?, `nom_doc_c` = ?, `estado` = ?, `id_user_act` = ?, `tipo_user_act` = ? , `fec_act` = ? WHERE `id_doc_c` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $idtipdoc, PDO::PARAM_INT);
                $sql->bindParam(2, $otro, PDO::PARAM_STR);
                $sql->bindParam(3, $nombre, PDO::PARAM_STR);
                $sql->bindParam(4, $estado_doc, PDO::PARAM_INT);
                $sql->bindParam(5, $iduser, PDO::PARAM_INT);
                $sql->bindParam(6, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(8, $iddoc, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $res = [
                        'estado' => 1,
                        'response' => 'Tipo de documento modificado correctamente',
                    ];
                } else {
                    $res = [
                        'estado' => 0,
                        'response' => 'Error: ' . $cdm->errorInfo()[2],
                    ];
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($res['response'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
                exit();
            }
        }
    } else if ($otro != $doc_sop['otro_tipo']) {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "UPDATE `seg_docs_contrato` SET `otro_tipo` = ?, `id_user_act` = ?, `tipo_user_act` = ? , `fec_act` = ? WHERE `id_doc_c` = ?";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $otro, PDO::PARAM_STR);
            $sql->bindParam(2, $iduser, PDO::PARAM_INT);
            $sql->bindParam(3, $tipuser, PDO::PARAM_STR);
            $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
            $sql->bindParam(5, $iddoc, PDO::PARAM_INT);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $res = [
                    'estado' => 1,
                    'response' => 'Tipo de documento modificado correctamente',
                ];
            } else {
                $res = [
                    'estado' => 0,
                    'response' => 'Error: ' . $cdm->errorInfo()[2],
                ];
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($res['response'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            exit();
        }
    }
    echo json_encode($res);
});
//DELETE  Borrar documento
$app->delete('/res/eliminar/documento/{id}', function (Request $request, Response $response) {
    $idD = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT * FROM seg_docs_tercero
                WHERE id_docster = '$idD'";
        $rs = $cmd->query($sql);
        $doc = $rs->fetch();
        $ruta = $doc['ruta_doc'] . $doc['nombre_doc'];
        $sql = "DELETE FROM seg_docs_tercero  WHERE id_docster = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $idD, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            unlink($ruta);
            echo json_encode(1);
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//DELETE  Borrar documento soporte  de contrato
$app->delete('/res/eliminar/documento_soporte_c/{id}', function (Request $request, Response $response) {
    $idD = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_doc_c`,`id_c_env`,`id_tipo_doc`,`otro_tipo`,`ruta_doc_c`,`nom_doc_c` FROM `seg_docs_contrato` WHERE `id_doc_c` = '$idD'";
        $rs = $cmd->query($sql);
        $doc = $rs->fetch();
        $ruta = $doc['ruta_doc_c'] . $doc['nom_doc_c'];
        $sql = "DELETE FROM `seg_docs_contrato` WHERE `id_doc_c` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $idD, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            unlink($ruta);
            echo json_encode(1);
        } else {
            echo json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
// DELETE Borrar resposabilidad economimca
$app->delete('/res/eliminar/resposabilidad/{id}', function (Request $request, Response $response) {
    $idt = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "DELETE FROM seg_responsabilidades_terceros  WHERE id_resptercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $idt, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode(1);
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
// DELETE Borrar actividad economimca
$app->delete('/res/eliminar/actividad/{id}', function (Request $request, Response $response) {
    $ida = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "DELETE FROM seg_actividad_terceros  WHERE id_actvtercero = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $ida, PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            echo json_encode(1);
        } else {
            json_encode($sql->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT Registrar cotizacion
$app->put('/res/nuevo/cotizacion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $items = count($data);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    $i = 0;
    $id_c = $data[0]['id_cot'];
    $nit = $data[0]['nit'];
    $objeto = $data[0]['objeto'];
    $est = 1;
    $i = 1;
    $lis_ter = $data[$i];

    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO `seg_cotiza_tercero` (`id_tercero`,`nit`,`id_cot`,`objeto`,`estado`,`fec_reg`) VALUES (?, ?, ?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $id_terc, PDO::PARAM_INT);
        $sql->bindParam(2, $nit, PDO::PARAM_STR);
        $sql->bindParam(3, $id_c, PDO::PARAM_INT);
        $sql->bindParam(4, $objeto, PDO::PARAM_STR);
        $sql->bindParam(5, $est, PDO::PARAM_STR);
        $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
        foreach ($lis_ter as $lt) {
            $id_terc = $lt;
            $sql->execute();
            if (!($cmd->lastInsertId() > 0)) {
                echo json_encode($sql->errorInfo()[2]);
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }

    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO `seg_cotizaciones`(`id_cot_empresa`,`nit_empresa`,`id_producto`,`id_bn_sv`,`bien_servicio`,`cantidad`,`val_estimado_unid`, `fec_reg`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $id_c, PDO::PARAM_INT);
        $sql->bindParam(2, $nit, PDO::PARAM_STR);
        $sql->bindParam(3, $id_prod, PDO::PARAM_INT);
        $sql->bindParam(4, $id_bnsv, PDO::PARAM_INT);
        $sql->bindParam(5, $bn_sv, PDO::PARAM_STR);
        $sql->bindParam(6, $cant, PDO::PARAM_INT);
        $sql->bindParam(7, $val_estimado, PDO::PARAM_STR);
        $sql->bindValue(8, $date->format('Y-m-d H:i:s'));
        for ($i = 2; $i < $items; $i++) {
            $id_prod = $data[$i]['id_producto'];
            $id_bnsv = $data[$i]['id_bn_sv'];
            $bn_sv = $data[$i]['bien_servicio'];
            $cant = $data[$i]['cantidad'];
            $val_estimado = $data[$i]['val_estimado_unid'];
            $sql->execute();
            if (!($cmd->lastInsertId() > 0)) {
                echo json_encode($sql->errorInfo()[2]);
            }
        }
        if ($i > 1) {
            echo json_encode(1);
        } else {
            echo json_encode('No pudo enviarse cotización');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

//Consulta Lista de empresas
$app->get('/res/listar/empresas', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    `seg_empresas`.`id_empresa`
                    , `seg_empresas`.`nit`
                    , `seg_empresas`.`dig_ver`
                    , `seg_empresas`.`correo`
                    , `seg_empresas`.`telefono`
                    , `seg_empresas`.`nombre`
                    , `seg_departamento`.`nombre_dpto`
                    , `seg_pais`.`nombre_pais`
                    , `seg_municipios`.`nom_municipio`
                    , `seg_empresas`.`direccion`
                FROM
                    `seg_empresas`
                    INNER JOIN `seg_municipios` 
                        ON (`seg_empresas`.`id_ciudad` = `seg_municipios`.`id_municipio`)
                    INNER JOIN `seg_pais` 
                        ON (`seg_empresas`.`id_pais` = `seg_pais`.`id_pais`)
                    INNER JOIN `seg_departamento` 
                        ON (`seg_departamento`.`id_pais` = `seg_pais`.`id_pais`) AND (`seg_empresas`.`id_dpto` = `seg_departamento`.`id_dpto`) AND (`seg_municipios`.`id_departamento` = `seg_departamento`.`id_dpto`)
                WHERE `seg_empresas`.`estado` = '1' ORDER BY `seg_empresas`.`nombre` ASC";
        $rs = $cmd->query($sql);
        $empresas = $rs->fetchAll();
        if (!empty($empresas)) {
            echo json_encode($empresas);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//consultar una empresa por id
$app->get('/res/listar/empresas/{id}', function (Request $request, Response $response) {
    $nit = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    `seg_empresas`.`id_empresa`
                    , `seg_empresas`.`nit`
                    , `seg_empresas`.`dig_ver`
                    , `seg_empresas`.`correo`
                    , `seg_empresas`.`telefono`
                    , `seg_empresas`.`nombre`
                    , `seg_departamento`.`nombre_dpto`
                    , `seg_pais`.`nombre_pais`
                    , `seg_municipios`.`nom_municipio`
                    , `seg_empresas`.`direccion`
                FROM
                    `seg_empresas`
                    INNER JOIN `seg_municipios` 
                        ON (`seg_empresas`.`id_ciudad` = `seg_municipios`.`id_municipio`)
                    INNER JOIN `seg_pais` 
                        ON (`seg_empresas`.`id_pais` = `seg_pais`.`id_pais`)
                    INNER JOIN `seg_departamento` 
                        ON (`seg_departamento`.`id_pais` = `seg_pais`.`id_pais`) AND (`seg_empresas`.`id_dpto` = `seg_departamento`.`id_dpto`) AND (`seg_municipios`.`id_departamento` = `seg_departamento`.`id_dpto`)
                WHERE `seg_empresas`.`estado` = '1' AND `nit`= '$nit' ";
        $rs = $cmd->query($sql);
        $empresas = $rs->fetch();
        if (!empty($empresas)) {
            echo json_encode($empresas);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Consulta Lista de empresas
$app->get('/res/listar/emprxter/{id}', function (Request $request, Response $response) {
    $id_ter = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT id_tercero, nit FROM (SELECT id_tercero, nit FROM seg_cotiza_tercero  WHERE id_tercero = '$id_ter') AS t
        GROUP BY nit";
        $rs = $cmd->query($sql);
        $empxter = $rs->fetchAll();
        if (!empty($empxter)) {
            echo json_encode($empxter);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Consulta cotizaciones por empresa
$app->get('/res/listar/cotizaciones/{id}', function (Request $request, Response $response) {
    $id_cot = explode('|', $request->getAttribute('id'));
    $id_ter = $id_cot[0];
    $nit = $id_cot[1];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_cot_ter`,`id_tercero`,`nit`,`id_cot`,`objeto`,`estado` FROM `seg_cotiza_tercero` WHERE `id_tercero` = '$id_ter' AND `nit`= '$nit'";
        $rs = $cmd->query($sql);
        $cotiz = $rs->fetchAll();
        if (!empty($cotiz)) {
            echo json_encode($cotiz);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Consulta contratos por empresa y tercero
$app->get('/res/listar/contratos/{id}', function (Request $request, Response $response) {
    $id_cont = explode('|', $request->getAttribute('id'));
    $id_ter = $id_cont[0];
    $nit = $id_cont[1];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_contratos`.`id_c`
                    , `seg_contratos`.`id_contrato`
                    , `seg_contratos`.`id_compra`
                    , `seg_contratos`.`nit_empresa`
                    , `seg_contratos`.`id_tercero`
                    , `seg_contratos`.`ruta_contrato`
                    , `seg_contratos`.`nombre_contrato`
                    , `seg_cotiza_tercero`.`objeto`
                    , `seg_cotiza_tercero`.`nit`
                    , `seg_cotiza_tercero`.`id_cot`
                    , `seg_contratos`.`estado`
                FROM
                    `seg_cotiza_tercero`
                    , `seg_contratos`
                WHERE `seg_contratos`.`nit_empresa` = `seg_cotiza_tercero`.`nit` AND `seg_contratos`.`id_compra` = `seg_cotiza_tercero`.`id_cot` 
                    AND `seg_contratos`.`id_tercero` = '$id_ter' AND `seg_contratos`.`nit_empresa` = '$nit'  GROUP BY  `seg_contratos`.`id_compra`";
        $rs = $cmd->query($sql);
        $contrat = $rs->fetchAll();
        if (!empty($contrat)) {
            echo json_encode($contrat);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//DEtalles de cotizacion
$app->get('/res/detalles/cotizacion/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $nit = $datos[1];
    $id_cot = $datos[2];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT 
                    `id_cot`,`id_cot_empresa`,`nit_empresa`,`id_producto`,`id_bn_sv`,`bien_servicio`,`cantidad`,`val_estimado_unid` 
                FROM `seg_cotizaciones` 
                WHERE  `id_cot_empresa` = '$id_cot' AND `nit_empresa` = '$nit'";
        $rs = $cmd->query($sql);
        $cotiz_det = $rs->fetchAll();
        if (!empty($cotiz_det)) {
            echo json_encode($cotiz_det);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//DEtalles de contrato enviado
$app->get('/res/detalles/contrato/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    $datos = [];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_contratos_enviados`.`id_c_env`
                    , `seg_contratos_enviados`.`id_c_rec`
                    , `seg_contratos`.`id_contrato`
                    , `seg_contratos`.`id_compra`
                    , `seg_contratos`.`nit_empresa`
                    , `seg_contratos`.`id_tercero`
                    , `seg_contratos_enviados`.`ruta_contrato`
                    , `seg_contratos_enviados`.`nombre_contrato`
                FROM
                    `seg_contratos_enviados`
                INNER JOIN `seg_contratos` 
                    ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
                WHERE `seg_contratos_enviados`.`id_c_rec` = '$id'  LIMIT 1";
        $rs = $cmd->query($sql);
        $det_contrato = $rs->fetch();
        if (!empty($det_contrato)) {
            $datos['contrato'] =  $det_contrato;
            $id_cont = $det_contrato['id_c_env'];
            $sql = "SELECT
                        `seg_docs_contrato`.`id_doc_c`
                        , `seg_docs_contrato`.`id_c_env`
                        , `seg_docs_contrato`.`id_tipo_doc`
                        , `seg_tipo_doc_soporte`.`descripcion`
                        , `seg_docs_contrato`.`otro_tipo`
                        , `seg_docs_contrato`.`ruta_doc_c`
                        , `seg_docs_contrato`.`nom_doc_c`
                        , `seg_docs_contrato`.`estado`
                    FROM
                        `seg_docs_contrato`
                    INNER JOIN `seg_tipo_doc_soporte` 
                        ON (`seg_docs_contrato`.`id_tipo_doc` = `seg_tipo_doc_soporte`.`id_doc_sop`) 
                    WHERE `seg_docs_contrato`.`id_c_env` = '$id_cont' ";
            $rs = $cmd->query($sql);
            $docs_contrato = $rs->fetchAll();
            $datos['docs'] = $docs_contrato;
            echo json_encode($datos);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//put nueva valores cotizacion
$app->put('/res/nuevo/cotizacion/valores', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    $id_tercero = $data['id_tercero'];
    $id_cotiza_tercero = $data['cot_ter'];
    $i = 0;
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO `seg_valor_cotizacion`(`id_tercero`,`id_cot_ter`,`valor`,`fec_reg`) VALUES (?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $id_tercero, PDO::PARAM_INT);
        $sql->bindParam(2, $id_cot_ter, PDO::PARAM_INT);
        $sql->bindParam(3, $valor, PDO::PARAM_STR);
        $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
        foreach ($data as $key => $value) {
            if (!($key == 'id_tercero' || $key == 'cot_ter')) {
                $id_cot_ter = $key;
                $valor = $data[$key];
                $sql->execute();
                if (!($cmd->lastInsertId() > 0)) {
                    echo json_encode($cmd->errorInfo()[2]);
                } else {
                    $i++;
                }
            }
        }
        if ($i > 0) {
            try {
                $estado = 2;
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE seg_cotiza_tercero SET estado = ?, fec_act = ? WHERE id_cot_ter = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $estado, PDO::PARAM_INT);
                $sql->bindValue(2, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(3, $id_cotiza_tercero, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    echo json_encode(1);
                } else {
                    echo json_encode($cmd->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        } else {
            echo json_encode('No se pudo enviar');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

//ver cotizacion enviada 
$app->get('/res/lista/cotizacion_enviada/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id_ter = $datos[0];
    $nit = $datos[1];
    $id_cot = $datos[2];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                `seg_valor_cotizacion`.`id_val_cot`
                , `seg_valor_cotizacion`.`id_tercero`
                , `seg_valor_cotizacion`.`id_cot_ter`
                , `seg_valor_cotizacion`.`cant_entrega`
                , `seg_cotizaciones`.`id_cot_empresa`
                , `seg_cotizaciones`.`nit_empresa`
                , `seg_cotizaciones`.`id_producto`
                , `seg_cotizaciones`.`id_bn_sv`
                , `seg_cotizaciones`.`bien_servicio`
                , `seg_cotizaciones`.`cantidad`
                , `seg_cotizaciones`.`val_estimado_unid`
                , `seg_valor_cotizacion`.`valor`
            FROM
                `seg_valor_cotizacion`
                INNER JOIN `seg_cotizaciones` 
                    ON (`seg_valor_cotizacion`.`id_cot_ter` = `seg_cotizaciones`.`id_cot`)
            WHERE `seg_valor_cotizacion`.`id_tercero` = '$id_ter' AND `seg_cotizaciones`.`id_cot_empresa`= '$id_cot' AND  `seg_cotizaciones`.`nit_empresa` = '$nit'";
        $rs = $cmd->query($sql);
        $cotiz_env = $rs->fetchAll();
        if (!empty($cotiz_env)) {
            echo json_encode($cotiz_env);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//Bajar cotizacion
$app->delete('/res/listar/bajar_cotizacion/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id = $datos[0];
    $nit = $datos[1];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT estado FROM `seg_cotiza_tercero` WHERE estado = '2' AND `nit` = '$nit' AND `id_cot` = '$id'";
        $rs = $cmd->query($sql);
        $bajar_cot = $rs->fetch();
        if (isset($bajar_cot)) {
            if ($bajar_cot['estado'] == '') {
                try {
                    $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                    $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                    $sql = "DELETE FROM `seg_cotizaciones`  WHERE `id_cot_empresa` = ? AND  `nit_empresa` = ? ";
                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $id, PDO::PARAM_INT);
                    $sql->bindParam(2, $nit, PDO::PARAM_STR);
                    $sql->execute();
                    if ($sql->rowCount() > 0) {
                        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                        $sql = "DELETE FROM `seg_cotiza_tercero`  WHERE `id_cot` = ? AND  `nit` = ? AND `estado` = '1' ";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $id, PDO::PARAM_INT);
                        $sql->bindParam(2, $nit, PDO::PARAM_STR);
                        $sql->execute();
                        if ($sql->rowCount() > 0) {
                            echo json_encode(1);
                        } else {
                            echo json_encode($cmd->errorInfo()[2]);
                        }
                    } else {
                        echo json_encode($cmd->errorInfo()[2]);
                    }
                    $cmd = null;
                } catch (PDOException $e) {
                    echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
                }
            } else {
                echo json_encode('No se puede bajar, un tercero replicó esta cotización');
            }
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

$app->get('/res/listar/estado_cotizacion/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id = $datos[0];
    $nit = $datos[1];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT estado FROM `seg_cotiza_tercero` WHERE estado = '2' AND `nit` = '$nit' AND `id_cot` = '$id'";
        $rs = $cmd->query($sql);
        $estado_cot = $rs->fetchAll();
        if (isset($estado_cot)) {
            echo json_encode($estado_cot[0]['estado']);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

$app->get('/res/listar/cot_recibidas/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id = $datos[0];
    $nit = $datos[1];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                `seg_cotiza_tercero`.`id_tercero`
                , `seg_cotiza_tercero`.`nit`
                , `seg_cotiza_tercero`.`id_cot`
                , `seg_cotiza_tercero`.`objeto`
                , `seg_cotiza_tercero`.`estado`
                , `seg_terceros`.`cc_nit`
                , `seg_terceros`.`apellido1`
                , `seg_terceros`.`apellido2`
                , `seg_terceros`.`nombre1`
                , `seg_terceros`.`nombre2`
                , `seg_terceros`.`razon_social`
                , `seg_departamento`.`nombre_dpto`
                , `seg_municipios`.`nom_municipio`
                , `seg_terceros`.`direccion`
                , `seg_terceros`.`telefono`
                , `seg_terceros`.`correo`
            FROM
                `seg_cotiza_tercero`
            INNER JOIN `seg_terceros` 
                ON (`seg_cotiza_tercero`.`id_tercero` = `seg_terceros`.`id_tercero`)
            INNER JOIN `seg_departamento` 
                ON (`seg_terceros`.`departamento` = `seg_departamento`.`id_dpto`)
            INNER JOIN `seg_municipios` 
                ON (`seg_municipios`.`id_departamento` = `seg_departamento`.`id_dpto`) AND (`seg_terceros`.`municipio` = `seg_municipios`.`id_municipio`)
            WHERE nit = '$nit' AND id_cot = '$id' AND estado = '2' GROUP BY `seg_cotiza_tercero`.`id_tercero`";
        $rs = $cmd->query($sql);
        $tercer_recibe = $rs->fetchAll();
        if (isset($tercer_recibe)) {
            echo json_encode($tercer_recibe);
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

$app->get('/res/listar/datos_cotiz_recibidas/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id = $datos[0];
    $nit = $datos[1];
    $tercero = $datos[2];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                `seg_valor_cotizacion`.`id_val_cot`
                , `seg_valor_cotizacion`.`id_tercero`
                , `seg_valor_cotizacion`.`id_cot_ter`
                , `seg_cotizaciones`.`id_cot_empresa`
                , `seg_cotizaciones`.`nit_empresa`
                , `seg_cotizaciones`.`id_producto`
                , `seg_cotizaciones`.`id_bn_sv`
                , `seg_cotizaciones`.`bien_servicio`
                , `seg_cotizaciones`.`cantidad`
                , `seg_cotizaciones`.`val_estimado_unid`
                , `seg_valor_cotizacion`.`valor`
            FROM
                `seg_valor_cotizacion`
            INNER JOIN `seg_cotizaciones` 
                ON (`seg_valor_cotizacion`.`id_cot_ter` = `seg_cotizaciones`.`id_cot`)
            WHERE  id_cot_empresa = '$id' AND nit_empresa = '$nit' AND id_tercero = '$tercero'";
        $rs = $cmd->query($sql);
        $valores_cotiza = $rs->fetchAll();
        if (isset($valores_cotiza)) {
            echo json_encode($valores_cotiza);
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});

//PUT Nuevo Documento soporte de contrato
$app->put('/res/nuevo/doc_soporte_contrato', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_cd = $data["id_cd"];
    $tipodoc = $data["tipodoc"];
    $tipodoc_otro = $data["otro"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $nom_archivo = $data["nom_archivo"];
    $temporal = $data["temporal"];
    $temporal = base64_decode($temporal);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_cd' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $ruta = '../../uploads/terceros/docs/' . $iduser . '/';
            if (!file_exists($ruta)) {
                $ruta = mkdir('../../uploads/terceros/docs/' . $iduser . '/', 0777, true);
                $ruta = '../../uploads/terceros/docs/' . $iduser . '/';
            }
            $res = file_put_contents("$ruta/$nom_archivo", $temporal);
            if (false !== $res) {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO `seg_docs_contrato`(`id_c_env`,`id_tipo_doc`,`otro_tipo`,`ruta_doc_c`,`nom_doc_c`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_cfinal, PDO::PARAM_INT);
                $sql->bindParam(2, $tipodoc, PDO::PARAM_INT);
                $sql->bindParam(3, $tipodoc_otro, PDO::PARAM_STR);
                $sql->bindParam(4, $ruta, PDO::PARAM_STR);
                $sql->bindParam(5, $nom_archivo, PDO::PARAM_STR);
                $sql->bindParam(6, $iduser, PDO::PARAM_INT);
                $sql->bindParam(7, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(8, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($cmd->lastInsertId() > 0) {
                    if ($tipodoc == '98') {
                        $estado = 2;
                        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                        $sql = "UPDATE `seg_supervisor_designado` SET `estado` = ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_contrato` = ?";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $estado, PDO::PARAM_INT);
                        $sql->bindParam(2, $iduser, PDO::PARAM_INT);
                        $sql->bindParam(3, $tipuser, PDO::PARAM_STR);
                        $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                        $sql->bindParam(5, $id_cfinal, PDO::PARAM_INT);
                        if (!($sql->execute())) {
                            echo json_encode($cdm->errorInfo()[2]);
                        }
                    }
                    echo json_encode(1);
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
            } else {
                echo json_encode('No se pudo adjuntar el archivo');
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se pudo adjuntar el archivo: no id');
    }
});
//listar tipos de novedad contratacion
$app->get('/res/listar/tipo_novedad', function (Request $request, Response $response) {
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT `id_novedad`,`descripcion` FROM `seg_tipo_novedad_contrato` WHERE `id_novedad` BETWEEN '1' AND '3'";
        $rs = $cmd->query($sql);
        $tip_novedad = $rs->fetchAll();
        if (!empty($tip_novedad)) {
            echo json_encode($tip_novedad);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT registar novedad -> adicion o prorroga.
$app->put('/res/nuevo/novedad/adicion_prorroga', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_novedad = $data['tip_novedad'];
    $val_adicion = $data['val_adicion'];
    $fec_adicion = $data['fec_adicion'];
    $cdp = $data['cdp'];
    $fini_pro = $data['fini_pro'];
    $ffin_pro = $data['ffin_pro'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_adi_pror`(`id_tip_nov`,`id_contrato`,`val_adicion`,`fec_adcion`,`cdp`,`fec_ini_prorroga`,`fec_fin_prorroga`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $tip_novedad, PDO::PARAM_INT);
            $sql->bindParam(2, $id_cfinal, PDO::PARAM_INT);
            $sql->bindParam(3, $val_adicion, PDO::PARAM_STR);
            $sql->bindParam(4, $fec_adicion, PDO::PARAM_STR);
            $sql->bindParam(5, $cdp, PDO::PARAM_INT);
            $sql->bindParam(6, $fini_pro, PDO::PARAM_STR);
            $sql->bindParam(7, $ffin_pro, PDO::PARAM_STR);
            $sql->bindParam(8, $observacion, PDO::PARAM_STR);
            $sql->bindParam(9, $iduser, PDO::PARAM_INT);
            $sql->bindParam(10, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(11, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});
//PUT registar novedad -> cesion.
$app->put('/res/nuevo/novedad/cesion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_nov = $data['tip_nov'];
    $fec_cesion = $data['fec_cesion'];
    $id_tercero = $data['id_tercero'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
        `seg_contratos`.`id_tercero`
        , `seg_contratos_enviados`.`id_c_env`
    FROM
        `seg_contratos_enviados`
        INNER JOIN `seg_contratos` 
            ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
            WHERE `seg_contratos_enviados`.`id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    $iD_terc = $id_cdev['id_tercero'];
    if ($iD_terc == $id_tercero) {
        echo json_encode('Tercero cesionario no puede ser él mismo');
        exit();
    } else if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_cesion`(`id_contrato`,`id_tipo_nov`,`id_tercero`,`fec_cesion`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id_cfinal, PDO::PARAM_INT);
            $sql->bindParam(2, $tip_nov, PDO::PARAM_INT);
            $sql->bindParam(3, $id_tercero, PDO::PARAM_INT);
            $sql->bindParam(4, $fec_cesion, PDO::PARAM_STR);
            $sql->bindParam(5, $observacion, PDO::PARAM_STR);
            $sql->bindParam(6, $iduser, PDO::PARAM_INT);
            $sql->bindParam(7, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(8, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});
//PUT registar novedad -> suspensión.
$app->put('/res/nuevo/novedad/suspension', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_nov = $data['tip_nov'];
    $fini_susp = $data['fini_susp'];
    $ffin_susp = $data['ffin_susp'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_suspension`,`id_contrato`,`id_tipo_nov`,`fec_inicia`,`fec_fin` FROM `seg_novedad_contrato_suspension` WHERE `id_contrato` = '$id_cfinal'  ORDER BY `fec_fin` DESC LIMIT 1";
        $rs = $cmd->query($sql);
        $suspensiones = $rs->fetch();
        if ($suspensiones['fec_fin'] != '' && $suspensiones['fec_fin'] >= $fini_susp) {
            echo json_encode('Fecha inicial de la nueva suspensión debe ser mayor a la fecha final de la última suspensión: ' . $suspensiones['fec_fin']);
            exit();
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_suspension`(`id_contrato`,`id_tipo_nov`,`fec_inicia`,`fec_fin`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id_cfinal, PDO::PARAM_INT);
            $sql->bindParam(2, $tip_nov, PDO::PARAM_INT);
            $sql->bindParam(3, $fini_susp, PDO::PARAM_STR);
            $sql->bindParam(4, $ffin_susp, PDO::PARAM_STR);
            $sql->bindParam(5, $observacion, PDO::PARAM_STR);
            $sql->bindParam(6, $iduser, PDO::PARAM_INT);
            $sql->bindParam(7, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(8, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});
//GET Consultar suspension de contrato por id de contrato
$app->get('/res/listar/suspension/{id}', function (Request $request, Response $response) {
    $id_contrato = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "SELECT `id_suspension`,`id_contrato`,`id_tipo_nov`,`fec_inicia`,`fec_fin` FROM `seg_novedad_contrato_suspension` WHERE `id_contrato` = '$id_cfinal'  ORDER BY `fec_fin` DESC LIMIT 1";
            $rs = $cmd->query($sql);
            $suspensiones = $rs->fetch();
            if (isset($suspensiones)) {
                echo json_encode($suspensiones);
            } else {
                echo json_encode($cmd->errorInfo()[2]);
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('Datos de contrato no disponible');
    }
});
//GET Consultar suspension de contrato por id reinicio
$app->get('/res/listar/suspension2/{id}', function (Request $request, Response $response) {
    $id_sus = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_suspension` FROM `seg_novedad_contrato_reinicio` WHERE `id_reinicio` = '$id_sus' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_s = $id_cdev['id_suspension'];
    if ($id_s != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "SELECT `id_suspension`,`id_contrato`,`id_tipo_nov`,`fec_inicia`,`fec_fin` FROM `seg_novedad_contrato_suspension` WHERE `id_suspension` = '$id_s'  ORDER BY `fec_fin` DESC LIMIT 1";
            $rs = $cmd->query($sql);
            $suspensiones = $rs->fetch();
            if (isset($suspensiones)) {
                echo json_encode($suspensiones);
            } else {
                echo json_encode($cmd->errorInfo()[2]);
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('Datos de contrato no disponible');
    }
});
//PUT registar novedad -> reinicio.
$app->put('/res/nuevo/novedad/reinicio', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_nov = $data['tip_nov'];
    $frein = $data['frein'];
    $id_suspension = $data['id_suspension'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_reinicio`(`id_tipo_nov`,`id_suspension`,`fec_reinicia`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $tip_nov, PDO::PARAM_INT);
            $sql->bindParam(2, $id_suspension, PDO::PARAM_INT);
            $sql->bindParam(3, $frein, PDO::PARAM_STR);
            $sql->bindParam(4, $observacion, PDO::PARAM_STR);
            $sql->bindParam(5, $iduser, PDO::PARAM_INT);
            $sql->bindParam(6, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});
//GET Consultar tipos de terminacion de contrato
$app->get('/res/listar/tipos_terminacion_contrato', function (Request $request, Response $response) {
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `id_tipo_term`,`descripcion` FROM `seg_tipo_terminacion_contrato`";
        $rs = $cmd->query($sql);
        $t_terminacion = $rs->fetchAll();
        if (isset($t_terminacion)) {
            echo json_encode($t_terminacion);
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT registar novedad -> terminacion.
$app->put('/res/nuevo/novedad/terminacion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_nov = $data['tip_nov'];
    $id_tt = $data['id_tt'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_terminacion`(`id_tipo_nov`,`id_t_terminacion`,`id_contrato`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $tip_nov, PDO::PARAM_INT);
            $sql->bindParam(2, $id_tt, PDO::PARAM_INT);
            $sql->bindParam(3, $id_cfinal, PDO::PARAM_STR);
            $sql->bindParam(4, $observacion, PDO::PARAM_STR);
            $sql->bindParam(5, $iduser, PDO::PARAM_INT);
            $sql->bindParam(6, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});
//PUT registar novedad -> liquidación.
$app->put('/res/nuevo/novedad/liquidacion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $tip_nov = $data['tip_nov'];
    $fec_liq = $data['fec_liq'];
    $tip_liq = $data['tip_liq'];
    $val_ctte = $data['val_ctte'];
    $val_ctta = $data['val_ctta'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];
    if ($id_cfinal != '') {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_novedad_contrato_liquidacion`(`id_tipo_nov`,`id_t_liq`,`id_contrato`,`fec_liq`,`val_cte`,`val_cta`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                    VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $tip_nov, PDO::PARAM_INT);
            $sql->bindParam(2, $tip_liq, PDO::PARAM_INT);
            $sql->bindParam(3, $id_cfinal, PDO::PARAM_INT);
            $sql->bindParam(4, $fec_liq, PDO::PARAM_STR);
            $sql->bindParam(5, $val_ctte, PDO::PARAM_STR);
            $sql->bindParam(6, $val_ctta, PDO::PARAM_STR);
            $sql->bindParam(7, $observacion, PDO::PARAM_STR);
            $sql->bindParam(8, $iduser, PDO::PARAM_INT);
            $sql->bindParam(9, $tipouser, PDO::PARAM_STR);
            $sql->bindValue(10, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    } else {
        echo json_encode('No se encontró contrato');
    }
});

//GET Consultar novedades de contrato
$app->get('/res/listar/novedades_contrato/{id}', function (Request $request, Response $response) {
    $id_contrato = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT`id_c_env` FROM `seg_contratos_enviados` WHERE `id_c_rec` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $id_cfinal = $id_cdev['id_c_env'];

    $list_nov = [];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_novedad_contrato_adi_pror`.`id_nov_con`
                    , `seg_novedad_contrato_adi_pror`.`id_tip_nov`
                    , `seg_tipo_novedad_contrato`.`descripcion`
                    , `seg_novedad_contrato_adi_pror`.`val_adicion`
                    , `seg_novedad_contrato_adi_pror`.`fec_adcion`
                    , `seg_novedad_contrato_adi_pror`.`cdp`
                    , `seg_novedad_contrato_adi_pror`.`fec_ini_prorroga`
                    , `seg_novedad_contrato_adi_pror`.`fec_fin_prorroga`
                    , `seg_novedad_contrato_adi_pror`.`observacion`
                FROM
                    `seg_novedad_contrato_adi_pror`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_adi_pror`.`id_tip_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                WHERE `seg_novedad_contrato_adi_pror`.`id_contrato` = '$id_cfinal'";
        $rs = $cmd->query($sql);
        $nov_adpro = $rs->fetchAll();
        if (isset($nov_adpro)) {
            $list_nov['adicion_prorroga'] = $nov_adpro;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $sql = "SELECT
                    `seg_novedad_contrato_cesion`.`id_cesion`
                    , `seg_tipo_novedad_contrato`.`descripcion`
                    , `seg_novedad_contrato_cesion`.`id_tipo_nov`
                    , `seg_novedad_contrato_cesion`.`id_tercero`
                    , `seg_terceros`.`cc_nit`
                    , `seg_terceros`.`apellido1`
                    , `seg_terceros`.`apellido2`
                    , `seg_terceros`.`nombre1`
                    , `seg_terceros`.`nombre2`
                    , `seg_terceros`.`razon_social`
                    , `seg_novedad_contrato_cesion`.`fec_cesion`
                    , `seg_novedad_contrato_cesion`.`observacion`
                FROM
                    `seg_novedad_contrato_cesion`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_cesion`.`id_tipo_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                    INNER JOIN `seg_terceros` 
                        ON (`seg_novedad_contrato_cesion`.`id_tercero` = `seg_terceros`.`id_tercero`)
                WHERE `seg_novedad_contrato_cesion`.`id_contrato` = '$id_cfinal'";
        $rs = $cmd->query($sql);
        $nov_cesion = $rs->fetchAll();
        if (isset($nov_cesion)) {
            $list_nov['cesion'] = $nov_cesion;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $sql = "SELECT
                    `seg_novedad_contrato_suspension`.`id_suspension`
                    , `seg_novedad_contrato_suspension`.`id_tipo_nov`
                    , `seg_tipo_novedad_contrato`.`descripcion`
                    , `seg_novedad_contrato_suspension`.`fec_inicia`
                    , `seg_novedad_contrato_suspension`.`fec_fin`
                    , `seg_novedad_contrato_suspension`.`observacion`
                FROM
                    `seg_novedad_contrato_suspension`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_suspension`.`id_tipo_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                WHERE `seg_novedad_contrato_suspension`.`id_contrato` = '$id_cfinal' ORDER BY `seg_novedad_contrato_suspension`.`fec_fin` DESC";
        $rs = $cmd->query($sql);
        $nov_suspension = $rs->fetchAll();
        if (isset($nov_suspension)) {
            $list_nov['suspension'] = $nov_suspension;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $sql = "SELECT
                    `seg_novedad_contrato_reinicio`.`id_reinicio`
                    , `seg_novedad_contrato_reinicio`.`id_tipo_nov`
                    , `seg_tipo_novedad_contrato`.`descripcion`
                    , `seg_novedad_contrato_reinicio`.`id_suspension`
                    , `seg_novedad_contrato_reinicio`.`fec_reinicia`
                    , `seg_novedad_contrato_reinicio`.`observacion`
                FROM
                    `seg_novedad_contrato_reinicio`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_reinicio`.`id_tipo_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                    INNER JOIN `seg_novedad_contrato_suspension` 
                        ON (`seg_novedad_contrato_reinicio`.`id_suspension` = `seg_novedad_contrato_suspension`.`id_suspension`)
                WHERE `seg_novedad_contrato_suspension`.`id_contrato` = '$id_cfinal' ORDER BY `seg_novedad_contrato_reinicio`.`fec_reinicia` DESC";
        $rs = $cmd->query($sql);
        $nov_reinicio = $rs->fetchAll();
        if (isset($nov_reinicio)) {
            $list_nov['reinicio'] = $nov_reinicio;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $sql = "SELECT
                    `seg_novedad_contrato_terminacion`.`id_terminacion`
                    , `seg_novedad_contrato_terminacion`.`id_tipo_nov`
                    , `seg_tipo_novedad_contrato`.`descripcion` 
                    , `seg_novedad_contrato_terminacion`.`id_t_terminacion`
                    , `seg_tipo_terminacion_contrato`.`descripcion`as `desc_ter`
                    , `seg_novedad_contrato_terminacion`.`observacion`
                FROM
                    `seg_novedad_contrato_terminacion`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_terminacion`.`id_tipo_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                    INNER JOIN `seg_tipo_terminacion_contrato` 
                        ON (`seg_novedad_contrato_terminacion`.`id_t_terminacion` = `seg_tipo_terminacion_contrato`.`id_tipo_term`)
                WHERE `seg_novedad_contrato_terminacion`.`id_contrato` = '$id_cfinal'";
        $rs = $cmd->query($sql);
        $nov_terminacion = $rs->fetchAll();
        if (isset($nov_terminacion)) {
            $list_nov['terminacion'] = $nov_terminacion;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        $sql = "SELECT
                    `seg_novedad_contrato_liquidacion`.`id_liquidacion`
                    , `seg_novedad_contrato_liquidacion`.`id_tipo_nov`
                    , `seg_novedad_contrato_liquidacion`.`id_t_liq`
                    , `seg_tipo_novedad_contrato`.`descripcion`
                    , `seg_novedad_contrato_liquidacion`.`fec_liq`
                    , `seg_novedad_contrato_liquidacion`.`val_cte`
                    , `seg_novedad_contrato_liquidacion`.`val_cta`
                    , `seg_novedad_contrato_liquidacion`.`observacion`
                FROM
                    `seg_novedad_contrato_liquidacion`
                    INNER JOIN `seg_tipo_novedad_contrato` 
                        ON (`seg_novedad_contrato_liquidacion`.`id_tipo_nov` = `seg_tipo_novedad_contrato`.`id_novedad`)
                WHERE `seg_novedad_contrato_liquidacion`.`id_contrato` = '$id_cfinal'";
        $rs = $cmd->query($sql);
        $nov_liquidacion = $rs->fetchAll();
        if (isset($nov_liquidacion)) {
            $list_nov['liquidacion'] = $nov_liquidacion;
        } else {
            echo json_encode($cmd->errorInfo()[2]);
        }
        echo json_encode($list_nov);
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//DELETE novedad de contrato
$app->delete('/res/eliminar/novedad/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    include $GLOBALS['conexion'];
    $id_nov = $datos[0];
    $novedad = $datos[1];
    $resp = 0;
    switch ($novedad) {
        case '1':
        case '2':
        case '3':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_adi_pror` WHERE `id_nov_con` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
        case '4':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_cesion` WHERE `id_cesion` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
        case '5':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_suspension` WHERE `id_suspension` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
        case '6':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_reinicio` WHERE `id_reinicio` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
        case '7':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_terminacion` WHERE `id_terminacion` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
        case '8':
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "DELETE FROM `seg_novedad_contrato_liquidacion` WHERE `id_liquidacion` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $id_nov, PDO::PARAM_INT);
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $resp = 1;
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
            break;
    }
    echo json_encode($resp);
});
//listar detalles de novedad contratacion
$app->get('/res/listar/detalles_novedad/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id_novedad = $datos[0];
    $opcion = $datos[1];
    include $GLOBALS['conexion'];
    switch ($opcion) {
        case 1:
        case 2:
        case 3:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT 
                            `id_nov_con`, `id_tip_nov`, `id_contrato`, `val_adicion`, `fec_adcion`, `cdp`, `fec_ini_prorroga`, `fec_fin_prorroga`, `observacion`
                        FROM
                            `seg_novedad_contrato_adi_pror`
                        WHERE `id_nov_con` = '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
        case 4:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT
                            `id_cesion`,`id_contrato`, `id_tipo_nov`, `id_tercero`, `fec_cesion`, `observacion`
                        FROM
                            `seg_novedad_contrato_cesion`
                        WHERE `id_cesion` = '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
        case 5:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT
                            `id_suspension`, `id_contrato`, `id_tipo_nov`, `fec_inicia`, `fec_fin`, `observacion`
                        FROM
                            `seg_novedad_contrato_suspension`
                        WHERE `id_suspension` = '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
        case 6:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT
                            `id_reinicio`, `id_tipo_nov`, `id_suspension`, `fec_reinicia`, `observacion`
                        FROM
                            `seg_novedad_contrato_reinicio`
                        WHERE `id_reinicio` =  '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
        case 7:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT
                            `id_terminacion`, `id_tipo_nov`, `id_t_terminacion`, `observacion`
                        FROM
                            `seg_novedad_contrato_terminacion`
                        WHERE `id_terminacion` =  '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
        case 8:
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
                $sql = "SELECT
                            `id_liquidacion`, `id_t_liq`, `id_tipo_nov`, `fec_liq`, `val_cte`, `val_cta`, `observacion`
                        FROM
                            `seg_novedad_contrato_liquidacion`
                        WHERE `id_liquidacion` = '$id_novedad'";
                $rs = $cmd->query($sql);
                $detalles = $rs->fetch();
                $response = isset($detalles) ? $detalles : $cmd->errorInfo()[2];
                $cmd = null;
            } catch (PDOException $e) {
                $response = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
            }
            break;
    }
    echo json_encode($response);
});

//PUT Actualizar novedad -> adicion o prorroga.
$app->put('/res/actualizar/novedad/adicion_prorroga', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_novedad = $data['id_novedad'];
    $tip_novedad = $data['tip_novedad'];
    $val_adicion = $data['val_adicion'];
    $fec_adicion = $data['fec_adicion'];
    $cdp = $data['cdp'];
    $fini_pro = $data['fini_pro'];
    $ffin_pro = $data['ffin_pro'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_novedad_contrato_adi_pror` SET `id_tip_nov` = ?,`val_adicion` = ?,`fec_adcion` = ?,`cdp` = ?,`fec_ini_prorroga` = ?,`fec_fin_prorroga` = ?,`observacion` = ?
                WHERE `id_nov_con`= ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $tip_novedad, PDO::PARAM_INT);
        $sql->bindParam(2, $val_adicion, PDO::PARAM_STR);
        $sql->bindParam(3, $fec_adicion, PDO::PARAM_STR);
        $sql->bindParam(4, $cdp, PDO::PARAM_INT);
        $sql->bindParam(5, $fini_pro, PDO::PARAM_STR);
        $sql->bindParam(6, $ffin_pro, PDO::PARAM_STR);
        $sql->bindParam(7, $observacion, PDO::PARAM_STR);
        $sql->bindParam(8, $id_novedad, PDO::PARAM_INT);
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($sql->rowCount() > 0) {
                $sql = "UPDATE `seg_novedad_contrato_adi_pror` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ?
                WHERE `id_nov_con`= ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                $sql->execute();
                echo json_encode(1);
            } else {
                echo json_encode('No se ha ingresado ningún cambio');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar novedad -> cesion.
$app->put('/res/actualizar/novedad/cesion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $id_novedad = $data['id_novedad'];
    $fec_cesion = $data['fec_cesion'];
    $id_tercero = $data['id_tercero'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_contratos_enviados`.`id_c_env`
                    , `seg_contratos`.`id_tercero`
                FROM
                    `seg_contratos_enviados`
                    INNER JOIN `seg_contratos` 
                        ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
                WHERE `seg_contratos_enviados`.`id_c_env` = '$id_contrato' LIMIT 1";
        $rs = $cmd->query($sql);
        $id_cdev = $rs->fetch();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    $iD_terc = $id_cdev['id_tercero'];
    if ($iD_terc == $id_tercero) {
        echo json_encode('Tercero cesionario no puede ser él mismo');
        exit();
    } else {
        try {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "UPDATE `seg_novedad_contrato_cesion` SET `id_tercero` = ?,`fec_cesion` = ?,`observacion` = ? WHERE `id_cesion` = ?";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id_tercero, PDO::PARAM_INT);
            $sql->bindParam(2, $fec_cesion, PDO::PARAM_STR);
            $sql->bindParam(3, $observacion, PDO::PARAM_STR);
            $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
            if (!($sql->execute())) {
                echo json_encode($sql->errorInfo()[2]);
            } else {
                if ($sql->rowCount() > 0) {
                    $sql = "UPDATE `seg_novedad_contrato_cesion` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ? WHERE `id_cesion`= ?";
                    $sql = $cmd->prepare($sql);
                    $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                    $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                    $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                    $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                    $sql->execute();
                    echo json_encode(1);
                } else {
                    echo json_encode('No se ha ingresado ningún cambio');
                }
            }
            $cmd = null;
        } catch (PDOException $e) {
            echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
        }
    }
});
//PUT registar actualizar -> suspensión.
$app->put('/res/actualizar/novedad/suspension', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $id_novedad = $data['id_novedad'];
    $fini_susp = $data['fini_susp'];
    $ffin_susp = $data['ffin_susp'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT `fec_fin` FROM `seg_novedad_contrato_suspension` WHERE `id_contrato` = '$id_contrato'  ORDER BY `fec_fin` DESC";
        $rs = $cmd->query($sql);
        $suspensiones = $rs->fetchAll();
        $filas = count($suspensiones);
        $cmd = null;
        //echo json_encode($suspensiones[1]['fec_fin'] . ' ?? ' . $fini_susp);
        //exit();
        if ($filas > 1 && $fini_susp <= $suspensiones[1]['fec_fin']) {
            echo json_encode('Fecha inicial de la nueva suspensión debe ser mayor a la fecha final de la última suspensión: ' . $suspensiones[1]['fec_fin']);
            exit();
        } else {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE `seg_novedad_contrato_suspension` SET `fec_inicia` = ?,`fec_fin` = ?,`observacion` = ? WHERE `id_suspension` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $fini_susp, PDO::PARAM_STR);
                $sql->bindParam(2, $ffin_susp, PDO::PARAM_STR);
                $sql->bindParam(3, $observacion, PDO::PARAM_STR);
                $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                if (!($sql->execute())) {
                    echo json_encode($sql->errorInfo()[2]);
                } else {
                    if ($sql->rowCount() > 0) {
                        $sql = "UPDATE `seg_novedad_contrato_suspension` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ? WHERE `id_suspension`= ?";
                        $sql = $cmd->prepare($sql);
                        $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                        $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                        $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                        $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                        $sql->execute();
                        echo json_encode(1);
                    } else {
                        echo json_encode('No se ha ingresado ningún cambio');
                    }
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar novedad -> reinicio.
$app->put('/res/actualizar/novedad/reinicio', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_novedad = $data['id_novedad'];
    $frein = $data['frein'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_novedad_contrato_reinicio` SET `fec_reinicia` = ?, `observacion` = ? WHERE `id_reinicio` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $frein, PDO::PARAM_STR);
        $sql->bindParam(2, $observacion, PDO::PARAM_STR);
        $sql->bindParam(3, $id_novedad, PDO::PARAM_INT);
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($sql->rowCount() > 0) {
                $sql = "UPDATE `seg_novedad_contrato_reinicio` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ? WHERE `id_reinicio`= ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                $sql->execute();
                echo json_encode(1);
            } else {
                echo json_encode('No se ha ingresado ningún cambio');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar novedad -> terminacion.
$app->put('/res/actualizar/novedad/terminacion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_novedad = $data['id_novedad'];
    $id_tt = $data['id_tt'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_novedad_contrato_terminacion` SET `id_t_terminacion` = ?, `observacion` = ? WHERE `id_terminacion` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $id_tt, PDO::PARAM_INT);
        $sql->bindParam(2, $observacion, PDO::PARAM_STR);
        $sql->bindParam(3, $id_novedad, PDO::PARAM_INT);
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($sql->rowCount() > 0) {
                $sql = "UPDATE `seg_novedad_contrato_terminacion` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ? WHERE `id_terminacion`= ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                $sql->execute();
                echo json_encode(1);
            } else {
                echo json_encode('No se ha ingresado ningún cambio');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT actualizar novedad -> liquidación.
$app->put('/res/actualizar/novedad/liquidacion', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_novedad = $data['id_novedad'];
    $fec_liq = $data['fec_liq'];
    $tip_liq = $data['tip_liq'];
    $val_ctte = $data['val_ctte'];
    $val_ctta = $data['val_ctta'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_novedad_contrato_liquidacion` SET `id_t_liq` = ?,`fec_liq` = ?,`val_cte` = ?,`val_cta` = ?,`observacion` = ? WHERE `id_liquidacion` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $tip_liq, PDO::PARAM_INT);
        $sql->bindParam(2, $fec_liq, PDO::PARAM_STR);
        $sql->bindParam(3, $val_ctte, PDO::PARAM_STR);
        $sql->bindParam(4, $val_ctta, PDO::PARAM_STR);
        $sql->bindParam(5, $observacion, PDO::PARAM_STR);
        $sql->bindParam(6, $id_novedad, PDO::PARAM_INT);
        if (!($sql->execute())) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($sql->rowCount() > 0) {
                $sql = "UPDATE `seg_novedad_contrato_liquidacion` SET `id_user_act` = ?,`tipo_user_act` = ?,`fec_act` = ? WHERE `id_liquidacion`= ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $iduser, PDO::PARAM_INT);
                $sql->bindParam(2, $tipouser, PDO::PARAM_STR);
                $sql->bindValue(3, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(4, $id_novedad, PDO::PARAM_INT);
                $sql->execute();
                echo json_encode(1);
            } else {
                echo json_encode('No se ha ingresado ningún cambio');
            }
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT registar novedad -> liquidación.
$app->put('/res/nuevo/contrato/designa_supervisor', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_contrato = $data['id_contrato'];
    $id_tercero = $data['id_tercero'];
    $fec_desig = $data['fec_desig'];
    $observacion = $data['observacion'];
    $iduser = $data['iduser'];
    $tipouser = $data['tipouser'];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "INSERT INTO `seg_supervisor_designado`(`id_tercero`,`id_contrato`,`fec_designacion`,`observacion`,`id_user_reg`,`tipo_user_reg`,`fec_reg`)
                VALUES(?, ?, ?, ?, ?, ?, ?)";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $id_tercero, PDO::PARAM_INT);
        $sql->bindParam(2, $id_contrato, PDO::PARAM_INT);
        $sql->bindParam(3, $fec_desig, PDO::PARAM_STR);
        $sql->bindParam(4, $observacion, PDO::PARAM_STR);
        $sql->bindParam(5, $iduser, PDO::PARAM_INT);
        $sql->bindParam(6, $tipouser, PDO::PARAM_STR);
        $sql->bindValue(7, $date->format('Y-m-d H:i:s'));
        $sql->execute();
        $id_sup = $cmd->lastInsertId();
        if ($id_sup > 0) {
            $response = [
                'status' => '1',
                'msg' => $id_sup
            ];
        } else {
            $response = [
                'status' => '0',
                'msg' => $sql->errorInfo()[2]
            ];
        }
        $cmd = null;
    } catch (PDOException $e) {
        $response['msg'] = $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
    }
    echo json_encode($response);
});
//listar contrato a supervisar
$app->get('/res/listar/contratos_supervisar/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    `seg_supervisor_designado`.`id_supervision`
                    , `seg_supervisor_designado`.`id_tercero`
                    , `seg_supervisor_designado`.`fec_designacion`
                    , `seg_supervisor_designado`.`observacion`
                    , `seg_supervisor_designado`.`id_contrato` AS `contrato`
                    , `seg_supervisor_designado`.`ruta`
                    , `seg_supervisor_designado`.`nombre`
                    , `seg_contratos_enviados`.`ruta_contrato`
                    , `seg_contratos_enviados`.`nombre_contrato`
                    , `seg_contratos`.`id_contrato`
                    , `seg_contratos`.`nit_empresa`
                FROM
                    `seg_supervisor_designado`
                    INNER JOIN `seg_contratos_enviados` 
                        ON (`seg_supervisor_designado`.`id_contrato` = `seg_contratos_enviados`.`id_c_env`)
                    INNER JOIN `seg_contratos` 
                        ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
                WHERE `seg_supervisor_designado`.`id_tercero` = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetchAll();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//listar docs contrato a supervisar
$app->get('/res/listar/contrato_acta/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    try {
        include $GLOBALS['conexion'];
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $sql = "SELECT
                    `seg_supervisor_designado`.`id_supervision`
                    , `seg_supervisor_designado`.`id_tercero`
                    , `seg_supervisor_designado`.`fec_designacion`
                    , `seg_supervisor_designado`.`observacion`
                    , `seg_supervisor_designado`.`id_contrato` AS `contrato`
                    , `seg_supervisor_designado`.`ruta`
                    , `seg_supervisor_designado`.`nombre`
                    , `seg_contratos_enviados`.`ruta_contrato`
                    , `seg_contratos_enviados`.`nombre_contrato`
                    , `seg_contratos`.`id_contrato`
                    , `seg_contratos`.`nit_empresa`
                    , `id_c_rec`
                    , `seg_supervisor_designado`.`estado`
                FROM
                    `seg_supervisor_designado`
                    INNER JOIN `seg_contratos_enviados` 
                        ON (`seg_supervisor_designado`.`id_contrato` = `seg_contratos_enviados`.`id_c_env`)
                    INNER JOIN `seg_contratos` 
                        ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
                WHERE `seg_supervisor_designado`.`id_contrato` = '$id'";
        $rs = $cmd->query($sql);
        $docs = $rs->fetch();
        if (!empty($docs)) {
            echo json_encode($docs);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//lista documentos contrato a supervisar
$app->get('/res/detalles/documentos/contrato_supervisar/{id}', function (Request $request, Response $response) {
    $id = $request->getAttribute('id');
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_docs_contrato`.`id_doc_c`
                    , `seg_docs_contrato`.`id_c_env`
                    , `seg_docs_contrato`.`id_tipo_doc`
                    , `seg_tipo_doc_soporte`.`descripcion`
                    , `seg_docs_contrato`.`otro_tipo`
                    , `seg_docs_contrato`.`ruta_doc_c`
                    , `seg_docs_contrato`.`nom_doc_c`
                    , `seg_docs_contrato`.`estado`
                FROM
                    `seg_docs_contrato`
                INNER JOIN `seg_tipo_doc_soporte` 
                    ON (`seg_docs_contrato`.`id_tipo_doc` = `seg_tipo_doc_soporte`.`id_doc_sop`) 
                WHERE `seg_docs_contrato`.`id_c_env` = '$id' ORDER BY `id_tipo_doc` DESC";
        $rs = $cmd->query($sql);
        $datos = $rs->fetchAll();
        echo json_encode($datos);
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//PUT entregar compra 
$app->PUT('/res/actualizar/entrega_compra', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    $iduser = $data[0]['iduser'];
    $tipuser = $data[0]['tipuser'];
    $pendientes = $data[0]['pendientes'];
    $id_contrato = $data[0]['id_contrato'];
    $total_entregas = 0;
    $cantidad_entrega = 0;
    include $GLOBALS['conexion'];
    foreach ($data as $key => $value) {
        if ($key != 0) {
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "INSERT INTO `seg_entrega_compra` (`id_prod`, `cantidad`,`cant_inicial`, `id_user_reg`, `tipo_user_reg`, `fec_reg`) VALUES (?, ?, ?, ?, ?, ?)";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $key);
                $sql->bindParam(2, $value);
                $sql->bindParam(3, $value);
                $sql->bindParam(4, $iduser);
                $sql->bindParam(5, $tipuser);
                $sql->bindValue(6, $date->format('Y-m-d H:i:s'));
                $sql->execute();
                if ($sql->rowCount() > 0) {
                    $total_entregas++;
                    $cantidad_entrega += intval($value);
                } else {
                    echo json_encode($sql->errorInfo()[2]);
                }
                $cmd = null;
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        }
    }
    if ($total_entregas > 0) {
        if (intval($cantidad_entrega) == intval($pendientes)) {
            $estado = 3;
            try {
                $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
                $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
                $sql = "UPDATE `seg_contratos` SET `estado`= ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_c` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $estado, PDO::PARAM_INT);
                $sql->bindParam(2, $iduser, PDO::PARAM_INT);
                $sql->bindParam(3, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(5, $id_contrato, PDO::PARAM_INT);
                $sql->execute();
            } catch (PDOException $e) {
                echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
            }
        }
        echo json_encode(1);
    } else {
        echo json_encode('No se puedo hacer la entrega');
    }
});
//lista contratado y entrega
//ver cotizacion enviada 
$app->get('/res/lista/compra_entregado/{id}', function (Request $request, Response $response) {
    $datos = explode('|', $request->getAttribute('id'));
    $id_ter = $datos[0];
    $nit = $datos[1];
    $id_cot = $datos[2];
    include $GLOBALS['conexion'];
    $response = [];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $consulta = "FROM 
                        (SELECT
                            `seg_valor_cotizacion`.`id_val_cot`
                            , `seg_valor_cotizacion`.`id_tercero`
                            , `seg_valor_cotizacion`.`id_cot_ter`
                            , `seg_valor_cotizacion`.`cant_entrega`
                            , `seg_cotizaciones`.`id_cot_empresa`
                            , `seg_cotizaciones`.`nit_empresa`
                            , `seg_cotizaciones`.`id_producto`
                            , `seg_cotizaciones`.`id_bn_sv`
                            , `seg_cotizaciones`.`bien_servicio`
                            , `seg_cotizaciones`.`cantidad` AS `cantid`
                            , `seg_cotizaciones`.`val_estimado_unid`
                            , `seg_valor_cotizacion`.`valor`
                        FROM
                            `seg_valor_cotizacion`
                            INNER JOIN `seg_cotizaciones` 
                                ON (`seg_valor_cotizacion`.`id_cot_ter` = `seg_cotizaciones`.`id_cot`)
                        WHERE `seg_valor_cotizacion`.`id_tercero` = '$id_ter' AND `seg_cotizaciones`.`id_cot_empresa`= '$id_cot' AND  `seg_cotizaciones`.`nit_empresa` = '$nit') AS t
                    LEFT JOIN `seg_entrega_compra` 
                        ON (`seg_entrega_compra`.`id_prod` = `t`.`id_val_cot`)";
        $sql = "SELECT
                    `seg_valor_cotizacion`.`id_val_cot`
                    , `seg_valor_cotizacion`.`id_tercero`
                    , `seg_valor_cotizacion`.`id_cot_ter`
                    , `seg_valor_cotizacion`.`cant_entrega`
                    , `seg_cotizaciones`.`id_cot_empresa`
                    , `seg_cotizaciones`.`nit_empresa`
                    , `seg_cotizaciones`.`id_producto`
                    , `seg_cotizaciones`.`id_bn_sv`
                    , `seg_cotizaciones`.`bien_servicio`
                    , `seg_cotizaciones`.`cantidad` AS `cantid`
                    , `seg_cotizaciones`.`val_estimado_unid`
                    , `seg_valor_cotizacion`.`valor`
                FROM
                    `seg_valor_cotizacion`
                    INNER JOIN `seg_cotizaciones` 
                        ON (`seg_valor_cotizacion`.`id_cot_ter` = `seg_cotizaciones`.`id_cot`)
                WHERE `seg_valor_cotizacion`.`id_tercero` = '$id_ter' AND `seg_cotizaciones`.`id_cot_empresa`= '$id_cot' AND  `seg_cotizaciones`.`nit_empresa` = '$nit'";
        $rs = $cmd->query($sql);
        $lista = $rs->fetchAll();
        $response['listado'] =  $lista;
        $sql = "SELECT 
                    `id_val_cot`
                    , `seg_entrega_compra`.`id_entrega`
                    , `seg_entrega_compra`.`cantidad` AS `cantidad_entrega`
                    , `seg_entrega_compra`.`estado`
                    , `seg_entrega_compra`.`fec_reg` 
                    , `seg_entrega_compra`.`fec_act`" . $consulta . " ORDER BY `seg_entrega_compra`.`fec_reg` ASC";
        $rs = $cmd->query($sql);
        $entregado = $rs->fetchAll();
        $response['entregas'] =  $entregado;
        $sql = "SELECT 
                    COUNT(`seg_entrega_compra`.`cantidad`) AS `entregas`" . $consulta . "GROUP BY `id_val_cot` ORDER BY entregas DESC LIMIT 1";
        $rs = $cmd->query($sql);
        $num_entregas = $rs->fetch();
        $response['num_entregas'] = $num_entregas;
        $response['nit'] =  $nit;
        $sql = "SELECT `id_c` FROM `seg_contratos` WHERE `id_compra`= '$id_cot' AND `nit_empresa`= '$nit' AND `id_tercero` = '$id_ter'  ";
        $rs = $cmd->query($sql);
        $id_cont = $rs->fetch();
        $id_c = $id_cont['id_c'];
        $response['id_c'] = $id_c;
        if (!empty($response)) {
            echo json_encode($response);
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//actualziar estado de elemento compra entregada
$app->put('/res/actualizar/estado_entrega', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    include $GLOBALS['conexion'];
    $id_contrato = $data['id_contrato'];
    $estado = $data['estado'];
    $id = $data["id"];
    $cant_rec = $data["cant_rec"];
    $iduser = $data["iduser"];
    $tipuser = $data["tipuser"];
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        $date = new DateTime('now', new DateTimeZone('America/Bogota'));
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "UPDATE `seg_entrega_compra` SET `cantidad`= ?, `estado`= ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_entrega` = ?";
        $sql = $cmd->prepare($sql);
        $sql->bindParam(1, $cant_rec, PDO::PARAM_INT);
        $sql->bindParam(2, $estado, PDO::PARAM_INT);
        $sql->bindParam(3, $iduser, PDO::PARAM_INT);
        $sql->bindParam(4, $tipuser, PDO::PARAM_STR);
        $sql->bindValue(5, $date->format('Y-m-d H:i:s'));
        $sql->bindParam(6, $id, PDO::PARAM_INT);
        $sql->execute();
        if (!($sql->rowCount() > 0)) {
            echo json_encode($sql->errorInfo()[2]);
        } else {
            if ($estado == 2) {
                $sql = "UPDATE `seg_contratos` SET `estado`= ?, `id_user_act` = ?, `tipo_user_act` = ?, `fec_act` = ? WHERE `id_c` = ?";
                $sql = $cmd->prepare($sql);
                $sql->bindParam(1, $estado, PDO::PARAM_INT);
                $sql->bindParam(2, $iduser, PDO::PARAM_INT);
                $sql->bindParam(3, $tipuser, PDO::PARAM_STR);
                $sql->bindValue(4, $date->format('Y-m-d H:i:s'));
                $sql->bindParam(5, $id_contrato, PDO::PARAM_INT);
                $sql->execute();
                if (!($sql->rowCount() > 0)) {
                    echo json_encode($cmd->errorInfo()[2]);
                }
            }
            echo json_encode(1);
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//nuevo certificadi form 220
//POST Nuevo tercero
$app->post('/res/nuevo/certificado/form220', function (Request $request, Response $response) {
    $data = json_decode(file_get_contents('php://input'), true);
    $archivo = $data['archivo'];
    $temporal = base64_decode($archivo);
    $vigencia = $data['vigencia'];
    $id_empleado = $data['id_empleado'];
    $tipo_certificado = $data['tipo_certificado'];
    $empresa = $data['empresa'];
    $id_user = $data['id_user'];
    $tipo_user = $data['tipo_user'];
    $nom_archivo = $tipo_certificado . '_' . date('YmdGis') . '.docx';
    $nom_archivo = strlen($nom_archivo) >= 101 ? substr($nom_archivo, 0, 100) : $nom_archivo;
    $date = new DateTime('now', new DateTimeZone('America/Bogota'));
    try {
        include $GLOBALS['conexion'];
        $ruta = '../../uploads/terceros/certificaciones/' . $id_empleado . '/';
        if (!file_exists($ruta)) {
            $ruta = mkdir('../../uploads/terceros/certificaciones/' . $id_empleado . '/', 0777, true);
            $ruta = '../../uploads/terceros/certificaciones/' . $id_empleado . '/';
        }
        $res = file_put_contents("$ruta/$nom_archivo", $temporal);
        if (false !== $res) {
            $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
            $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            $sql = "INSERT INTO `seg_certificaciones` (`id_tercero`, `id_tipo_certf`, `ruta`, `nombre_archivo`, `nit_empresa`, `vigencia`, `id_user_reg`, `tipo_user_reg`, `fec_reg`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $sql = $cmd->prepare($sql);
            $sql->bindParam(1, $id_empleado, PDO::PARAM_INT);
            $sql->bindParam(2, $tipo_certificado, PDO::PARAM_INT);
            $sql->bindParam(3, $ruta, PDO::PARAM_STR);
            $sql->bindParam(4, $nom_archivo, PDO::PARAM_STR);
            $sql->bindParam(5, $empresa, PDO::PARAM_STR);
            $sql->bindParam(6, $vigencia, PDO::PARAM_STR);
            $sql->bindParam(7, $id_user, PDO::PARAM_INT);
            $sql->bindParam(8, $tipo_user, PDO::PARAM_STR);
            $sql->bindValue(9, $date->format('Y-m-d H:i:s'));
            $sql->execute();
            if ($cmd->lastInsertId() > 0) {
                echo json_encode(1);
            } else {
                echo json_encode($sql->errorInfo()[2]);
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
//consultar certificaciones form 220
$app->get('/res/consulta/certificados/{id}', function (Request $request, Response $response) {
    $data = explode('|', $request->getAttribute('id'));
    $nit = $data[0];
    $vigencia = $data[1];
    $tipo = $data[2];
    $id_emplea = $data[3];
    include $GLOBALS['conexion'];
    try {
        $cmd = new PDO("$bd_driver:host=$bd_servidor;dbname=$bd_base;$charset", $bd_usuario, $bd_clave);
        $cmd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $sql = "SELECT
                    `seg_certificaciones`.`id_certificacion`
                    , `seg_certificaciones`.`id_tercero`
                    , `seg_certificaciones`.`id_tipo_certf`
                    , `seg_certificaciones`.`ruta`
                    , `seg_certificaciones`.`nombre_archivo`
                    , `seg_certificaciones`.`nit_empresa`
                    , `seg_certificaciones`.`vigencia`
                    , `seg_terceros`.`tipo_doc`
                    , `seg_terceros`.`cc_nit`
                    , `seg_terceros`.`apellido1`
                    , `seg_terceros`.`apellido2`
                    , `seg_terceros`.`nombre1`
                    , `seg_terceros`.`nombre2`
                    , `seg_terceros`.`razon_social`
                FROM
                    `docs_api`.`seg_certificaciones`
                    INNER JOIN `docs_api`.`seg_terceros` 
                        ON (`seg_certificaciones`.`id_tercero` = `seg_terceros`.`id_tercero`)
                WHERE `seg_certificaciones`.`nit_empresa` = '$nit' AND `seg_certificaciones`.`vigencia` = '$vigencia' AND `seg_certificaciones`.`id_tipo_certf`= '$tipo'";
        $rs = $cmd->query($sql);
        $forms = $rs->fetchAll();
        if (!empty($forms)) {
            if ($id_emplea == '') {
                echo json_encode($forms);
            } else {
                $key = array_search($id_emplea, array_column($forms, 'id_tercero'));
                if ($key !== false) {
                    echo json_encode($forms[$key]['ruta'] . $forms[$key]['nombre_archivo']);
                } else {
                    echo json_encode('0');
                }
            }
        } else {
            echo json_encode('0');
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
