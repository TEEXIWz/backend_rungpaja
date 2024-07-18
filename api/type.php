<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/type', function (Request $request, Response $response) {
    $conn = $GLOBALS['conn'];
    $sql = 'select * from type';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

$app->get('/type/{id}', function (Request $request, Response $response, $args) {
    $conn = $GLOBALS['conn'];
    $sql = 'select * from type where tid = ?';
    $stmt = $conn->prepare($sql);

    $stmt->bind_param('s', $args['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = array();
    foreach ($result as $row) {
        array_push($data, $row);
    }

    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});

$app->get('/type/name/{name}', function (Request $request, Response $response, $args) {
    $name = '%'.$args['name'].'%';
    $conn = $GLOBALS['conn'];
    $sql = 'select * from type where name like ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        array_push($data, $row);
    }
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK));
    return $response
        ->withHeader('Content-Type', 'application/json; charset=utf-8')
        ->withStatus(200);
});


$app->post('/type', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);

    $conn = $GLOBALS['conn'];
    $sql = 'insert into type (name) values (?)';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $jsonData['name']);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {

        $data = ["affected_rows" => $affected, "last_tid" => $conn->insert_id];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

$app->put('/type/{id}', function (Request $request, Response $response, $args) {
    $json = $request->getBody();
    $jsonData = json_decode($json, true);
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'update type set name=? where tid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $jsonData['name'], $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});

$app->delete('/type/{id}', function (Request $request, Response $response, $args) {
    $id = $args['id'];
    $conn = $GLOBALS['conn'];
    $sql = 'delete from type where tid = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    if ($affected > 0) {
        $data = ["affected_rows" => $affected];
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
});
