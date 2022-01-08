<?php
define('DATABASE_CONFIG', "../../config/database.ini");
define('DIR_IMAGES', "../../images");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../vendor/autoload.php';

use OpenForms\Quiz\Quiz;
use OpenForms\Quiz\QuizRepository;
use OpenForms\User\AuthenticateUser;
use OpenForms\Session\SessionCreator;
use OpenForms\Session\SessionValidator;
use OpenForms\SolvedQuiz\SolvedQuiz;
use OpenForms\SolvedQuiz\SolvedQuizRepository;
use OpenForms\User\UserRepository;
use OpenForms\User\User;
use OpenForms\Utils\Octopus\Octopus;

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');

$app = new Octopus();
$app->post('/login', function ($req, $res) {
    $_authenticateUser = new AuthenticateUser($req->body['email'], $req->body['password']);
    $_user = $_authenticateUser();
    if ($_user) {
        $_sessionCreator = new SessionCreator($_user);

        $res->json($_sessionCreator(), true, 'Usuario logueado con exito');
        return;
    }
    $res->json(null, false, 'No se pudo completar el proceso de logueo');
});

$app->post('/signup', function ($req, $res) {
    $_ur = new UserRepository();
    $_user = new User(
        uniqid(),
        $req->body['name'],
        $req->body['email'],
        $req->body['password'],
        1
    );

    $_user = $_ur->save(['id' => uniqid(), ...$req->body, 'enable' => 1]);

    if ($_user) {
        $_sessionCreator = new SessionCreator($_user);

        $res->json($_sessionCreator(), true, 'Usuario logueado con exito');
        return;
    }
    $res->json(null, false, 'No se pudo completar el proceso de logueo');
});


$app->route('/quiz/:id')
    ->get(function ($res, $req, $id) {
        $_qr = new QuizRepository();
        $quiz = $_qr->findById_not_solved($id);
        if ($quiz) {
            $res->json($quiz, true, 'Quiz correcto');
        }
    })
    ->post(function ($res, $req) {
        $_session_validator = new SessionValidator($req->cookies['session_id']);
        $_session = $_session_validator();
        if ($_session) {
            $_qr = new QuizRepository();
            $_quiz = new Quiz(
                $req->body['id'],
                $_session['user_id'],
                $req->body['name'],
                date('Y-m-d H:i:s'),
                $req->body['duration'],
                json_encode($req->body['questions']),
                json_encode($req->body['answers']),
                1
            );
            $new_quiz = $_qr->save($_quiz);
            $res->json($new_quiz, true, 'Quiz creado exitosamente');
            return;
        }
        $res->json(null, false, 'Quiz no se pudo crear');
    });


$app->get('/:user/quizes', function ($req, $res, $user) {
    $_qr = new QuizRepository();
    $quizes = $_qr->findByUser($user);
    $res->json($quizes, true, 'lista de quizes');
});

$app->post('/solved_quiz', function ($req, $res) {
    $_sqr = new  SolvedQuizRepository();
    $_solved_quiz = new SolvedQuiz(
        null,
        $req->body['quiz_id'],
        null,
        date('Y-m-d H:i:s'),
        $req->body['duration'],
        json_encode($req->body['responses']),
        1
    );
    if (isset($req->cookies['session_id'])) {
        $_session_validator = new SessionValidator($req->cookies['session_id']);
        $_session = $_session_validator();
        if ($_session) {
            $_solved_quiz->user_id = $_session['user_id'];
            $new_quiz = $_sqr->save($_solved_quiz);
            $res->json($new_quiz, true, 'Respuestas guardadas exitosamente');
            return;
        }
    } else {
        $new_quiz = $_sqr->save($_solved_quiz);
        $res->json($new_quiz, true, 'Respuestas guardadas exitosamente');
        return;
    }


    $res->json(null, false, 'Error guradando las respuestas');
});

$app->get('statistics/:quiz_id', function ($req, $res, $quiz_id) {
    // pcntl_async_signals()
    if (!isset($req->cookies['session_id'])) {
        $res->json(null, false, 'session inexistente');
        return;
    }

    $_session_validator = new SessionValidator($req->cookies['session_id']);
    $_session = $_session_validator();
    if (!$_session) {
        $res->json(null, false, 'Session expirada o invalida inicie session de nuevo');
        return;
    }

    $_sqr = new SolvedQuizRepository();
    $_quiz_repo = new QuizRepository();
    $_quiz = $_quiz_repo->findById($quiz_id);
    $_responses = $_sqr->findByQuizId($quiz_id);
    $data['responses'] = $_responses;
    $data['quiz'] = $_quiz;
    if (!$_quiz && !$_responses) {
        $res->json(null, false, 'Error al obtener los datos');
        return;
    }

    $res->json($data, true, 'Estadisticas obtenidas exitosamente');
    return;
});
