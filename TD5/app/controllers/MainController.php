<?php
namespace controllers;
use models\Product;
use models\Section;
use Ubiquity\attributes\items\router\Route;
use Ubiquity\controllers\auth\AuthController;
use Ubiquity\controllers\auth\WithAuthTrait;
use Ubiquity\orm\DAO;
use Ubiquity\utils\http\URequest;

/**
 * Controller MainController
 **/
class MainController extends ControllerBase{
    use WithAuthTrait;


    #[Route('_default',name: 'home')]
	public function index(){
        $u=$this->_getAuthController()->_getActiveUser();
        $promos=DAO::getAll(Product::class,'promotion<?',false,[0]);
        $this->loadView("MainController/index.html",["promos"=>$promos]);
	}

    public function initialize() {
        parent::initialize();
        $this->jquery->getHref('a[data-target]','',['listenerOn'=>'body','hasLoader'=>'internal-x']);
    }


    protected function getAuthController(): AuthController
    {
        return new AuthController($this);
    }

    #[Route('store',name: 'store')]
	public function store($content='')
    {
        $sections = DAO::getAll(Section::class, '', ['products']);
        $this->jquery->renderView('MainController/store.html', compact('sections', 'content'));
    }

    #[Route('section/{id}',name: 'section')]
    public function sectionsMenu($id){
        $section=DAO::getById(Section::class,$id,['products']);
        if(!URequest::isAjax()) {
            $content=$this->loadDefaultView(compact('section'),true);
            $this->store($content);
            return;
        }
        $this->loadDefaultView(compact('section'));
    }

    #[Route('product/{id}',name: 'product')]
    public function product($id){
        $product = DAO::getById(Product::class, $id, ['products']);
        $this->loadView("MainController/detailsProduct.html");
    }

    #[Route('product/{idS}/{idP}',name: 'section')]
    public function detailsProduit($idS, $idP){
        $section=DAO::getById(Section::class,$idS,['products']); // ??
        $product=DAO::getById(Product::class,$idP,['products']);

        if(!URequest::isAjax()) {
            $content=$this->loadDefaultView(compact('product'),true);
            $this->store($content);
            return;
        }

        $this->loadDefaultView(compact('product'));
    }
}