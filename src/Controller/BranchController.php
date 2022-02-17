<?php

namespace Src\Controller;

use Src\Model\Tree;
use Src\TableGateways\BranchGateway;

class BranchController
{
    private $db;
    private $requestMethod;
    private $id;
    private $branchGateway;

    /**
     * @param $db
     * @param $requestMethod
     */
    public function __construct($db, $requestMethod)
    {
        $this->db = $db;
        $this->requestMethod = $requestMethod;
        $this->branchGateway = new BranchGateway($db);
    }

    /**
     *
     * @return void
     * @api
     */
    public function processRequest()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriMas = explode('/', $uri);
        switch ($this->requestMethod) {
            case 'GET':
                $response = $this->getAllBranches();
                break;
            case 'POST':
                $response = $this->createBranchFromRequest();
                break;
            case 'PUT':
                $response = $this->updateBranchFromRequest($uriMas[count($uriMas) - 1]);
                break;
            case 'DELETE':
                $response = $this->deleteBranch($uriMas[count($uriMas) - 1]);
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

    /**
     * @return array
     */
    private function getAllBranches(): array
    {
        $result = $this->branchGateway->findAll();
        $tree = new Tree();
        $result = $tree->getTree($result);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = json_encode($result);
        return $response;
    }

    /**
     *
     * @return array
     *
     */
    private function createBranchFromRequest(): array
    {
        $input = $_POST;
        $this->branchGateway->insert($input);
        $response['status_code_header'] = 'HTTP/1.1 201 Created';
        $response['body'] = null;
        return $response;
    }

    /**
     * @param $input
     * @return array
     */
    function httpParseNodeData($input): array
    {
        $parsed_input = preg_replace('/-{5,}[0-9]{5,}\s+[A-Z :;-]+="/ui', '', $input);
        $parsed_input = preg_replace('/-{5,}[0-9]{5,}--/ui', '', $parsed_input);
        $parsed_input = preg_replace('/"/', '', $parsed_input);
        $parsed_input = preg_replace('/\s+/', ' ', $parsed_input);
        $parsed_input = trim($parsed_input);

        preg_match_all('/\S+\s\S+/', $parsed_input, $parsed_input_array);

        $parsed_input_array = $parsed_input_array[0];
        $result_parsed_input_array = [];

        foreach ($parsed_input_array as $value) {
            $key_value_array = explode(' ', $value);
            $result_parsed_input_array[$key_value_array[0]] = $key_value_array[1];
        }

        return ($result_parsed_input_array);
    }

    /**
     * @param $id
     * @return array
     */
    private function updateBranchFromRequest($id): array
    {
        $result = $this->branchGateway->find($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $input1 = file_get_contents("php://input");
        $input = $this->httpParseNodeData($input1);

        if (!$this->validateBranch($input)) {
            return $this->unproceableEntityResponse();
        }

        $this->branchGateway->update($id, $input);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    /**
     * @param $id
     * @return array
     */
    private function deleteBranch($id): array
    {
        $result = $this->branchGateway->find($id);

        if (!$result) {
            return $this->notFoundResponse();
        }

        $this->branchGateway->delete($id);
        $response['status_code_header'] = 'HTTP/1.1 200 OK';
        $response['body'] = null;
        return $response;
    }

    /**
     * @param $input
     * @return bool
     */
    private function validateBranch($input): bool
    {
        if (!isset($input['name'])) {
            return false;
        }
        return true;
    }

    /**
     * @return array
     */
    private function unprocessableEntityResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 422 Unprocessable Entity';
        $response['body'] = json_encode([
            'error' => 'Invalid input'
        ]);
        return $response;
    }

    /**
     * @return array
     */
    private function notFoundResponse(): array
    {
        $response['status_code_header'] = 'HTTP/1.1 404 Not Found';
        $response['body'] = null;
        return $response;
    }
}