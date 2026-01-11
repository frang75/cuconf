<?php
// // 1. Recuperamos el parámetro 'mensaje' de la URL (ej: saludo.php?mensaje=Hola)
// // Usamos el operador de fusión de null (??) para dar un valor por defecto si no existe
// $mensajeRecibido = $_GET['mensaje'] ?? 'No se recibió ningún mensaje';

// // 2. IMPORTANTE: Sanitizamos la salida para evitar ataques XSS (Cross-Site Scripting)
// // Siempre que imprimas algo que viene del usuario, usa htmlspecialchars
// $mensajeSeguro = htmlspecialchars($mensajeRecibido, ENT_QUOTES, 'UTF-8');

// // 3. Creamos un array asociativo con la estructura deseada
// $data = [
//     "response" => $mensajeSeguro
// ];

// // 4. Convertimos el array a una cadena JSON válida y la imprimimos
// echo json_encode($data); -->


// // // 3. Respondemos al navegador
// // echo "El mensaje enviado es: " . $mensajeSeguro;


/**
 * PoC: Servicio de Respuesta Basada en Documentos Locales
 */

class PilotNotebookLM {
    private $apiKey = 'TU_API_KEY_AQUÍ';
    private $docsPath = __DIR__ . '/../docs/'; // Carpeta con tus PDFs/TXTs

//     // // 1. Intentar cargar la configuración
//     // if (file_exists('config.php')) {
//     //     require_once 'config.php';
//     // } else {
//     //     die("Error: El archivo config.php no existe.");
//     // }

    public function __construct() {
        // Usamos las constantes definidas en config.php
        $this->apiKey = GEMINI_API_KEY;
    }

    public function getKey() {
        return $this->apiKey;
    }

    public function models() {
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $this->apiKey;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function responder($pregunta) {

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;
        $parts = [];
        //echo "PATH!!!!" . $this->docsPath;
        foreach (glob($this->docsPath . "*.{pdf,txt}", GLOB_BRACE) as $archivo) {
            //echo "Archivo!!!!" . $archivo;
            $mimeType = (pathinfo($archivo, PATHINFO_EXTENSION) === 'pdf') ? 'application/pdf' : 'text/plain';
            $parts[] = [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => base64_encode(file_get_contents($archivo))
                ]
            ];
        }

//         // 3. Añadimos la pregunta al final
        $parts[] = ["text" => $pregunta];
        $payload = [
            "contents" => [["parts" => $parts]],
            "system_instruction" => [
                "parts" => [["text" => "Eres un asistente de prueba. Responde ÚNICAMENTE con la información de los documentos adjuntos. Si no lo sabes, di que no está en los documentos."]]
            ],
            "generationConfig" => [
                "temperature" => 0.1, // Menor creatividad = más fidelidad al texto
            ]
        ];

        $ch = curl_init($url);
        $body = json_encode($payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //echo $body;

        $response = curl_exec($ch);
        curl_close($ch);

        //echo "EHHHHHHHHHHHHHHHH!!!!!!!!" . $response . "  YA!!!!!!!!!!!!!!!!!";

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Error: " . $response;
    }


}

//     public function responder($pregunta) {

//         $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

//         // 1. Preparamos las partes del mensaje
//         $parts = [];

//         // 2. Leemos los documentos locales y los adjuntamos "Inline"
//         // Como son pequeños (<20MB total), podemos enviarlos directamente sin subirlos antes
//         foreach (glob($this->docsPath . "*.{pdf,txt}", GLOB_BRACE) as $archivo) {
//             $mimeType = (pathinfo($archivo, PATHINFO_EXTENSION) === 'pdf') ? 'application/pdf' : 'text/plain';
//             $parts[] = [
//                 "inline_data" => [
//                     "mime_type" => $mimeType,
//                     "data" => base64_encode(file_get_contents($archivo))
//                 ]
//             ];
//         }

//         // 3. Añadimos la pregunta al final
//         $parts[] = ["text" => $pregunta];

//         // 4. Payload con instrucciones de restricción (Efecto NotebookLM)
//         $payload = [
//             "contents" => [["parts" => $parts]],
//             "system_instruction" => [
//                 "parts" => [["text" => "Eres un asistente de prueba. Responde ÚNICAMENTE con la información de los documentos adjuntos. Si no lo sabes, di que no está en los documentos."]]
//             ],
//             "generationConfig" => [
//                 "temperature" => 0.1, // Menor creatividad = más fidelidad al texto
//             ]
//         ];

//         // 5. Ejecución vía cURL
//         $ch = curl_init($url);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
//         curl_setopt($ch, CURLOPT_POST, 1);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//         $response = curl_exec($ch);
//         curl_close($ch);

//         $data = json_decode($response, true);
//         return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Error: " . $response;
//     }
//}

// // --- MODO DE PRUEBA ---
require_once __DIR__ . '/./config.php';

$mensajeRecibido = $_GET['mensaje'] ?? 'No se recibió ningún mensaje';
$mensajeSeguro = htmlspecialchars($mensajeRecibido, ENT_QUOTES, 'UTF-8');
$asistente = new PilotNotebookLM();
//echo "Key" . $asistente->getKey();
$resp = $asistente->responder($mensajeSeguro);
$data = [
    "response" => $resp
];

// // 4. Convertimos el array a una cadena JSON válida y la imprimimos
echo json_encode($data);


// echo "Resp:" . $asistente->responder($mensajeSeguro);

// echo $asistente->responder("¿Qué dice el documento sobre el horario de oficina?");