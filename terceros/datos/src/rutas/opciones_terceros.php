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
                WHERE cc_nit  IN ($ids)";
        $rs = $cmd->query($sql);
        $terceros = $rs->fetchAll();
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
    if (!empty($terceros)) {
        echo json_encode($terceros);
    } else {
        echo json_encode('0');
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
                WHERE id_tercero = '$id'";
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
    $contrasena = $data['passT'];
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
        if ($cmd->lastInsertId() > 0) {
            echo json_encode('1');
        } else {
            echo json_encode(print_r($sql->errorInfo()[2]));
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
            echo json_encode(print_r($sql->errorInfo()[2]));
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
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
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
            echo json_encode(print_r($sql->errorInfo()[2]));
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
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
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
                    echo json_encode('1');
                } else {
                    echo json_encode(print_r($sql->errorInfo()[2]));
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
                    echo json_encode('1');
                } else {
                    print_r($sql->errorInfo()[2]);
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
                echo json_encode('1');
            } else {
                echo json_encode(print_r($sql->errorInfo()[2]));
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
            if ($cmd->lastInsertId() > 0) {
                echo json_encode('1');
            } else {
                echo json_encode(print_r($sql->errorInfo()[2]));
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
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
                        echo json_encode(print_r($sql->errorInfo()[2]));
                    } else {
                        echo json_encode('1');;
                    }
                    $cmd = null;
                } catch (PDOException $e) {
                    echo $e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage();
                }
            } else {
                echo json_encode(print_r($sql->errorInfo()[2]));
            }
        } else {
            echo json_encode('No se pudo adjuntar el archivo');
        }
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
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
                            echo json_encode('1');
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
                        echo json_encode('1');
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
            echo json_encode('1');
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
            echo json_encode('1');
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
            echo json_encode('1');
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
                echo json_encode(print_r($sql->errorInfo()[2]));
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
                echo json_encode(print_r($sql->errorInfo()[2]));
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
                WHERE `seg_empresas`.`estado` = '1'";
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
                    `docs_api`.`seg_contratos_enviados`
                INNER JOIN `docs_api`.`seg_contratos` 
                    ON (`seg_contratos_enviados`.`id_c_rec` = `seg_contratos`.`id_c`)
                WHERE `seg_contratos_enviados`.`id_c_rec` = '$id'  LIMIT 1";
        $rs = $cmd->query($sql);
        $det_contrato = $rs->fetch();
        if (!empty($det_contrato)) {
            $datos['contrato'] =  $det_contrato;
            $id_cont = $det_contrato['id_c_env'];
            $sql = "SELECT `id_doc_c`,`id_c_env`,`ruta_doc_c`,`nom_doc_c` FROM `seg_docs_contrato` WHERE `id_c_env` = '$id_cont' ";
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
                    echo json_encode(print_r($cmd->errorInfo()[2]));
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
                            echo json_encode(print_r($cmd->errorInfo()[2]));
                        }
                    } else {
                        echo json_encode(print_r($cmd->errorInfo()[2]));
                    }
                    $cmd = null;
                } catch (PDOException $e) {
                    echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
                }
            } else {
                echo json_encode('No se puede bajar, un tercero replicó esta cotización');
            }
        } else {
            echo json_encode(print_r($cmd->errorInfo()[2]));
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
            echo json_encode(0);
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
            echo json_encode(print_r($cmd->errorInfo()[2]));
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
            echo json_encode(print_r($cmd->errorInfo()[2]));
        }
        $cmd = null;
    } catch (PDOException $e) {
        echo json_encode($e->getCode() == 2002 ? 'Sin Conexión a Mysql (Error: 2002)' : 'Error: ' . $e->getMessage());
    }
});
