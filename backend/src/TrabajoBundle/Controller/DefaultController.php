<?php

namespace TrabajoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller {
    
    /**
     * @Route("/", name="nodos")
     * @Method({"GET"})
     */
    public function indexAction() {
        header('Access-Control-Allow-Origin: *'); 
//        Código real
//        $directorio = '/';
//        $directorio == '' ? $directorio = '/' : $directorio = $_GET["directorio"];
//        
//        Código de Prueba
        $directorio = '/';
        $directorio == '' ? $directorio = '/' : $directorio = 'C:\xampp\htdocs\server\src\GPowerM' . '/';
        $dirs = DefaultController::getTree($directorio);
        $string = json_encode($dirs);
        $aux = str_replace("[]", '""', $string);
        echo $aux;
        return $this->render('TrabajoBundle:Default:index.html.twig');
    }
    
    /**
     * @Route("/nodo/adicionar")
     * @Method({"POST"})
     */
    public function addAction() {
        header('Access-Control-Allow-Origin: *'); 
//        Código real
//
//        $nombre = $_POST["name"];
//        $nodoP = $_POST["nodoP"];
//        $tipo = $_POST["tipo"];
//        $directorio = $_POST["nodoP"];
//        
//        Código de Prueba

        $nombre = "Projects";
        $tipo = 'dir';
        $nodoP = 'C:/xampp/htdocs/server/src/GPowerM/Informations/';
        $directorio = 'C:/xampp/htdocs/server/src/GPowerM/';

        
        $adds = DefaultController::sendNodeTree($directorio, $tipo, $nombre, $nodoP);
        $res = json_encode($adds);
        $aux = str_replace("[]", '""', $res);
        echo $aux;
        return $this->redirectToRoute('nodos');
    }
    
    /**
     * @Route("/nodo/mover")
     * @Method({"POST"})
     */
    public function moveAction() {
        header('Access-Control-Allow-Origin: *'); 
//        Código real
//
//        $rutaO = $_POST["rutaO"];
//        $rutaD = $_POST["rutaD"];
//        $directorio = $_POST["directorio"];
//
//        Código de Prueba

        $rutaO = "C:/xampp/htdocs/server/src/GPowerM/Informations";
        $rutaD = 'C:/xampp/htdocs/server/src/GPowerM/Other_Folder';
        $directorio = 'C:/xampp/htdocs/server/src/GPowerM/';
        
        $moves = DefaultController::moveNodeTree($directorio, $rutaO, $rutaD);
        $res = json_encode($moves);
        $aux = str_replace("[]", '""', $res);
        echo $aux;
        return $this->redirectToRoute('nodos');
    }
    
    /**
     * @Route("/nodo/eliminar")
     * @Method({"DELETE"})
     */
    public function delAction() {
        header('Access-Control-Allow-Origin: *'); 
//        Código real
//
//        $ruta = $_DELETE["ruta"];
//        $directorio = $_DELETE["directorio"];
//        
//        Código de Prueba

        $ruta = 'C:/xampp/htdocs/server/src/GPowerM/Other_Folder';
        $directorio = 'C:/xampp/htdocs/server/src/GPowerM/';
        
        $dels = DefaultController::deleteNodeTree($directorio, $ruta);
        $res = json_encode($dels);
        $aux = str_replace("[]", '""', $res);
        echo $aux;
        return $this->redirectToRoute('nodos');
    }

    public static function getTree($directorio) {
        
        $res = [];
        
        if (filetype($directorio) == "file") {
            $type = mime_content_type($directorio);
            header("Content-disposition: attachment; filename=$directorio");
            header("Content-type: $type");
            readfile($directorio);
            return;
        } else {
            $dir = dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
        }
        while (($archivo = $dir->read()) !== false) {
            
            if ($archivo[0] == ".") {
                continue;
            }
            if (is_dir($directorio . $archivo)) {
                array_push($res, array(
                    "name" => $archivo,
                    "type" => filetype($directorio . $archivo),
                    "src" => $directorio . $archivo,
                    "children" => DefaultController::getTree($directorio . $archivo . "/")
                ));
            } else if (is_readable($directorio . $archivo)) {
                array_push($res, array(
                    "name" => $archivo,
                    "type" => filetype($directorio . $archivo),
                    "src" => $directorio . $archivo,
                    "children" => ""
                ));
            }
        }
        $dir->close();
        return $res;
    }

    public static function sendNodeTree($directorio, $tipo, $nombre, $nodoP) {

        $res = array();
        if ($tipo == 'dir') {
            if (!file_exists($nombre)) {
                mkdir($nodoP . $nombre, 0777, true);
                $res[] = DefaultController::getTree($directorio);
            }
        } else if ($tipo == 'file') {
            if (file_exists($nombre)) {
                $mensaje = "El Archivo $nombre se ha modificado";
            } else {
                $mensaje = "El Archivo $nombre se ha creado";
            }
            if ($archivo = fopen($nodoP . $nombre, "a")) {
                if (fwrite($archivo, "")) {
                    echo "";
                } else {
                    echo "";
                }
                fclose($archivo);
                $res[] = DefaultController::getTree($directorio);
            }
        }
        return $res;
    }
    
    public static function fullCopy ($source, $target) {
        if(is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while(FALSE !== ($entry = $d->read())) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if (is_dir($Entry)) {
                    DefaultController::fullCopy($Entry, $target . '/' . $entry);
                    continue;
                }
                copy($Entry, $target . '/' . $entry);
            }
            $d->close();
        } else {
            copy($source, $target);
        }
    }
    
    public static function rmDir_rf ($carpeta) {
        foreach (glob($carpeta . '/*') as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {
                DefaultController::rmDir_rf($archivos_carpeta);
            } else {
                unlink($archivos_carpeta);
            }
        }
        rmdir($carpeta);
    }

    public static function moveNodeTree($directorio, $rutaO, $rutaD) {
        
        $res = array();

        if(filetype($rutaO) == 'dir') {
            DefaultController::fullCopy($rutaO, $rutaD);
            DefaultController::rmDir_rf($rutaO);
            mkdir($rutaO, 0777, true);
            $res[] = DefaultController::getTree($directorio);
        } else if(filetype($rutaO) == 'file') {
            copy($rutaO, $rutaD);
            unlink($rutaO);
            $res[] = DefaultController::getTree($directorio);
        }
        
        return $res;
    }

    public static function deleteNodeTree($directorio, $ruta) {
        
        $res = array();
        if (filetype($ruta) == 'dir') {
            DefaultController::rmDir_rf($ruta);
            $res[] = DefaultController::getTree($directorio);
        } else if (filetype($ruta) == 'file') {
            unlink($ruta);
            $res[] = DefaultController::getTree($directorio);
        }
        return $res;
    }

}
