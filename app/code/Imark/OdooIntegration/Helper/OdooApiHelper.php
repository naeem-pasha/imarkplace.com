<?php

namespace Imark\OdooIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class OdooApiHelper extends AbstractHelper
{

    protected $odooUrl = "http://192.168.0.174:8069"; // Odoo server URL
    //protected $odooUrl = "http://192.168.156.77:8069"; // Odoo server URL
    //protected $odooUrl = "http://54.173.65.42:8069"; // Odoo server URL
    

    public function getSessionId()
    {
        $authData = json_encode([
            "jsonrpc" => "2.0",
            "method" => "call",
            "params" => [
                "db" => "db_greenedtech",
                "login" => "naeem@greensfin.com",
                "password" => "Greens@321"
            ],
            "id" => null
        ]);

        $ch = curl_init($this->odooUrl . "/web/session/authenticate");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authData);
        curl_setopt($ch, CURLOPT_HEADER, true);  // Include headers in the output
        curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output

        // Execute the request and capture the response
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("cURL Error: " . curl_error($ch));
        }

        // Separate headers and body
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($result, 0, $header_size);
        //$body = substr($result, $header_size);

       // print "<pre>";
        // Print raw header and body for debugging
       // echo "Header: ";
       // print_r($header);
       // echo "\nBody: ";
       // print_r($body);

        // Extract the session_id from Set-Cookie header
        preg_match('/Set-Cookie: session_id=([^;]+)/', $header, $matches);
        $session_id = $matches[1] ?? null;

        if ($session_id) {
            return $session_id;
        } else {
            echo "Session ID not found in cookies.";
        }

        curl_close($ch);
    }

    public function createUserInOdoo($orderData)
    {
        $payload = json_encode([
            "jsonrpc" => "2.0",
            "method" => "call",
            "params" => [
                'db' => 'db_greenedtech',            // Odoo database
                'login' => 'naeem@greensfin.com',      // Odoo admin login
                'password' => 'Greens@321', // Odoo admin password
                'session_url' => $this->odooUrl . "/web/session/authenticate",
                'user_name' => $orderData['customer_name'],
                'user_email' => $orderData['customer_email'],
                'course_id' => $orderData['course_id'],   // Assuming course_id is from Magento order attributes
            ],
            JSON_UNESCAPED_SLASHES,
            "id" => null
        ]);

        // Send request to Odoo
        return $this->makeJsonRpcRequest("/magento/create_user", $payload);
    }

    private function makeJsonRpcRequest($endpoint, $payload)
    {

        // Authenticate and retrieve the session ID dynamically
        $sessionId = $this->getSessionId();
        $url = $this->odooUrl . $endpoint;
        $ch = curl_init($url);

        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Host:192.168.0.174:8069',
            'Cookie: session_id='.$sessionId
            //'Cookie: session_id=' . $sessionId // Dynamically set the session_id in the Cookie header
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new \Exception("cURL Error: " . curl_error($ch));
        }

        curl_close($ch);

        return json_decode($result, true);
    }
}
