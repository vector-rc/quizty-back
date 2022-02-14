<?php


define('DB_CONFIG', "../../config/database.ini");

define('JWT_PUBLIC_KEY', "../../config/jwt/public.pem");
define('JWT_PRIVATE_KEY', "../../config/jwt/private.pem");
define('DIR_IMAGES', "../../images");

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../vendor/autoload.php';

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

use Quizty\Quiz\Quiz;
use Quizty\Quiz\QuizRepository;
use Quizty\User\AuthenticateUser;
use Quizty\Session\SessionCreator;
use Quizty\Session\SessionValidator;
use Quizty\SolvedQuiz\SolvedQuiz;
use Quizty\SolvedQuiz\SolvedQuizRepository;
use Quizty\User\UserRepository;
use Quizty\User\User;
use Quizty\Utils\JWT;
use Quizty\Utils\Octopus\Octopus;
use Quizty\Utils\Octopus\Response;
use Quizty\Utils\Octopus\Request;

header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');



$app = new Octopus();

$app->middleware(function(Request $req,Response $res){
    $req->hola="esto es un hola";
    $jwt=$req->body['jwt'];
    $isValid=JWT::validate($jwt);
    if(!$isValid) $res->status(401)->json(null,false,'error en el middelware');

    $jwtDecoded=JWT::parser($jwt);
    $req->user=$jwtDecoded;
    return $req;
});


$app->post('/login', function (Request $req,Response $res) {

    $jwt = JWT::create($req->body,[],60);
    $_authenticateUser = new AuthenticateUser($req->body['email'], $req->body['password']);
    $_user = $_authenticateUser();
    if ($_user) {

        $_sessionCreator = new SessionCreator($_user);
        $data = $_sessionCreator();
        $data['jwt'] = $jwt;
        $data['hola']=$req->hola;

        $res->json($data, true, 'Usuario logueado con exito');
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
                uniqid('', true),
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


$app->get('/my_quizes', function ($req, $res) {
    $_session_validator = new SessionValidator($req->cookies['session_id']);
    $_session = $_session_validator();
    if ($_session) {
        $_qr = new QuizRepository();
        $quizes = $_qr->findMinimizedByUser($_session['user_id']);
        $res->json($quizes, true, 'lista de quizes');
        return;
    }

    $res->json(null, false, 'Error al obterner los quizes de este usuario');
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
            $quizRepo = new QuizRepository();
            $quiz = $quizRepo->findById($req->body['quiz_id']);
            if ($quiz['user_id'] === $_session['user_id']) {
                $res->json(null, false, 'Error:No puede resolver su propio quiz.');
                return;
            }
            $_solved_quiz->user_id = $_session['user_id'];
            $new_solved_quiz = $_sqr->save($_solved_quiz);
            $res->json($new_solved_quiz, true, 'Respuestas guardadas exitosamente');
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

$app->post('/test_jwt',function($req,$res){
    $jwt=$req->body['jwt'];
    $jwtDecoded=JWT::parser($jwt);
    //print_r((array)$jwtDecoded);
    $isValid=JWT::validate($jwt);
    $data['jwt_decoded']=$jwtDecoded;
    $data['jwt_valid']=$isValid;
    //var_dump($data);
   // print_r($data['jwt_decoded'].getClaims());
    return $res->json($data, true, 'No se pudo completar el proceso de logueo');
});