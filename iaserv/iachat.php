<?php
/**
 * PoC: Servicio de Respuesta Basada en Documentos Locales
 */

class PilotNotebookLM {
    private $apiKey = 'TU_API_KEY_AQUÍ';
    private $docsPath = __DIR__ . '../docs/'; // Carpeta con tus PDFs/TXTs

    // 1. Intentar cargar la configuración
    if (file_exists('config.php')) {
        require_once 'config.php';
    } else {
        die("Error: El archivo config.php no existe.");
    }

    public function responder($pregunta) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $this->apiKey;

        // 1. Preparamos las partes del mensaje
        $parts = [];

        // 2. Leemos los documentos locales y los adjuntamos "Inline"
        // Como son pequeños (<20MB total), podemos enviarlos directamente sin subirlos antes
        foreach (glob($this->docsPath . "*.{pdf,txt}", GLOB_BRACE) as $archivo) {
            $mimeType = (pathinfo($archivo, PATHINFO_EXTENSION) === 'pdf') ? 'application/pdf' : 'text/plain';
            $parts[] = [
                "inline_data" => [
                    "mime_type" => $mimeType,
                    "data" => base64_encode(file_get_contents($archivo))
                ]
            ];
        }

        // 3. Añadimos la pregunta al final
        $parts[] = ["text" => $pregunta];

        // 4. Payload con instrucciones de restricción (Efecto NotebookLM)
        $payload = [
            "contents" => [["parts" => $parts]],
            "system_instruction" => [
                "parts" => [["text" => "Eres un asistente de prueba. Responde ÚNICAMENTE con la información de los documentos adjuntos. Si no lo sabes, di que no está en los documentos."]]
            ],
            "generationConfig" => [
                "temperature" => 0.1, // Menor creatividad = más fidelidad al texto
            ]
        ];

        // 5. Ejecución vía cURL
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Error: " . $response;
    }
}

// --- MODO DE PRUEBA ---
$asistente = new PilotNotebookLM();
echo $asistente->responder("¿Qué dice el documento sobre el horario de oficina?");