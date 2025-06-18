$route = $_GET['route'] ?? 'form';

require_once '../app/controllers/DiagnosisController.php';
$controller = new DiseaseController();

switch ($route) {
    case 'diagnose':
        $controller->diagnose();
        break;
    default:
        $controller->diagnose();
        break;
}