<?php
namespace Src\Controller;

use Src\TableGateways\BranchGateway;

class BranchController {

    private $db;
    private $requestMethod;
    private $id;

    private $branchGateway;

    public function __construct($db, $requestMethod)
    {

        $this->db = $db;
        $this->requestMethod = $requestMethod;

        $this->branchGateway = new BranchGateway($db);
    }


    public function processRequest()
    {
        /*
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriMas = explode( '/', $uri );
*/

        var_dump($this->requestMethod);
        die();
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getAllBranches();
                break;
            case 'POST':
                $response = $this->createBranchFromRequest();
                break;
            case 'PUT':

                $response = $this->updateBranchFromRequest($_GET[0]);
                break;
            case 'DELETE':
                $response = $this->deleteBranch();
                break;
            default:
                $response = $this->notFoundResponse();
                break;
        }
        header($response['status_code_header']);
        if ($response['body']) {
            echo $response['body'];
        }
    }

    private function getAllBranches()
    {
        $result = $this->branchGateway->findAll();
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     *
     * @api
     *
     * @return array\
     */
    private function createBranchFromRequest()
    {
        $input = $_POST;

        /*
        if (! $this->validateBranch($input)) {
            return $this->unprocessableEntityResponse();
        }
        */

        $this->branchGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    private function updateBranchFromRequest($id)
    {
        $result = $this->branchGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $input = $_POST;
        var_dump($input);
        die();
        if (! $this->validateBranch($input)) {
            return $this->unprocessableEntityResponse();
        }
        $this->branchGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    private function deleteBranch($id)
    {
        $result = $this->branchGateway->find($id);
        if (! $result) {
            return $this->notFoundResponse();
        }
        $this->branchGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }


    private function validateBranch($input)
    {
        if (! isset($input['name'])) {
            return false;
        }


        return true;
    }


    private function unprocessableEntityResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    private function notFoundResponse()
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}