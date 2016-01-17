<?php

namespace Multiple\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;
use Phalcon\Acl\Resource as AclResource;

/**
 * SecurityPlugin
 * ระบบกำหนดสิทธิ์การเข้าถึงข้อมูล
 */

class SecurityPlugin extends Plugin {
        
    private $_status;
    private $_enable = true;
    private $_module;

    private $acl;
    private $_roles;

    // ข้อมูล "บทบาทหน้าที่"
    /*
     * บทบาทหน้าที่
     * แบ่งเป็น 4 ระดับ
     * -- 1. ระดับ Super Admin หัวหน้าผู้ดูแลระบบ
     * -- 2. ระดับ Admin ผู้ดูแลระบบ
     * -- 3. ระดับ Vip สมาชิกพิเศษ
     * -- 4. ระดับ Member สมาชิก
     * -- 5. ระดับ Guests ผู้ใช้ทั่วไป
     */
    
    private $roles = array(
        'superadmin'    => 'SuperAdmin',
        'admin'         => 'Admins',
        'vip'           => 'Vips',
        'members'       => 'Members',
        'guests'        => 'Guests',
    );

    private $arrAcl = array(
        'frontend' => array(
            'SuperAdmin' => array(),
            'Admins' => array(),
            'Vips' => array(),
            'Members' => array(),
            'Guests' => array(
                'main'    => array('index'),
            ),
        ),
        /*
        'moduleName' => array(
            'roleName' => array(
                'controllerName' => array('actionName','actionName'),
                'controllerName' => array('actionName','actionName'),
            ),
            'Admins' => array(),
            'Vips' => array(),
            'Members' => array(),
            'Guests' => array(),
        ),
        */
    );
    
    // เปิด / ปิด ระบบกำหนดสิทธิ์การเข้าถึง
    public function __construct() {
        $baseSystem = new \CBaseSystem();
        $this->_status = $baseSystem->securityStart;
    }
    
    // เปิด / ปิด ระบบอัพเดทตลอดเวลา
    public function setModule($module = 'frontend') {
        if(!empty($module)){
            $this->_module = $module;
        }
    }

    /**
     * Returns an existing or new access control list
     * @returns AclList
     */
    
    public function getAcl(){
        
        if (!isset($this->persistent->acl) || !empty($this->_enable)) {
            
            $this->acl = new AclList();
            $this->acl->setDefaultAction(Acl::DENY);
             
            // ดึงข้อมูล "บทบาทหน้าที่" 
            foreach ($this->roles as $roleKey => $roleName) {
                // ext. $roles['superadmin'] = new Role('SuperAdmin');
                $roles[$roleKey] = new Role($roleName);
            }
            $this->_roles = $roles;
            
            // ลงทะเบียน "บทบาทหน้าที่"
            foreach ($roles as $role) {
                $this->acl->addRole($role);
            }
            
            // หัวหน้าผู้ดูแลระบบ
            $this->setRoleSuperAdmin();
            
            // ผู้ดูแลระบบ
            $this->setRoleAdmins();
            
            // สมาชิกพิเศษ
            $this->setRoleVips();
            
            // สมาชิก
            $this->setRoleMembers();
            
            // พื้นฐาน
            // $this->setRoleUser();
            
            // ทั่วไป
            $this->setRolePublic();
            
            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $this->acl;
        }
        
        return $this->persistent->acl;
    }
    private function setRoleSuperAdmin(){
        // หัวหน้าผู้ดูแลระบบ
        if(!empty($this->arrAcl[$this->_module]['SuperAdmin'])){
            $privateResources = $this->arrAcl[$this->_module]['SuperAdmin'];
            foreach ($privateResources as $resource => $actions) {
                $this->acl->addResource(new Resource($resource), $actions);
            }
            // Setting Role Super Admin
            foreach ($privateResources as $resource => $actions) {
                foreach ($actions as $action){
                    $this->acl->allow('SuperAdmin', $resource, $action);
                }
            }
        }
    }
    private function setRoleAdmins(){
        // ผู้ดูแลระบบ
        if(!empty($this->arrAcl[$this->_module]['Admins'])){
            $adminResources = $this->arrAcl[$this->_module]['Admins'];
            foreach ($adminResources as $resource => $actions) {
                $this->acl->addResource(new Resource($resource), $actions);
            }
            // Setting Role Super Admin
            foreach ($adminResources as $resource => $actions) {
                foreach ($actions as $action){
                    $this->acl->allow('Admins', $resource, $action);
                }
            }
        }
    }
    private function setRoleVips(){
        // สมาชิกพิเศษ
        if(!empty($this->arrAcl[$this->_module]['Vips'])){
            $vipResources = $this->arrAcl[$this->_module]['Vips'];
            foreach ($vipResources as $resource => $actions) {
                $this->acl->addResource(new Resource($resource), $actions);
            }
            // Setting Role Super Admin
            foreach ($vipResources as $resource => $actions) {
                foreach ($actions as $action){
                    $this->acl->allow('Vips', $resource, $action);
                }
            }
        }
    }
    private function setRoleMembers(){
        // สมาชิก
        if(!empty($this->arrAcl[$this->_module]['Members'])){
            $memberResources = $this->arrAcl[$this->_module]['Members'];
            foreach ($memberResources as $resource => $actions) {
                $this->acl->addResource(new Resource($resource), $actions);
            }
            // Setting Role Super Admin
            foreach ($memberResources as $resource => $actions) {
                foreach ($actions as $action){
                    $this->acl->allow('Members', $resource, $action);
                }
            }
        }
    }
    private function setRolePublic(){
        // ทั่วไป
        if(!empty($this->arrAcl[$this->_module]['Guests'])){
            $publicResources = $this->arrAcl[$this->_module]['Guests'];
            foreach ($publicResources as $resource => $actions) {
                $this->acl->addResource(new Resource($resource), $actions);
            }
            // Grant access to public areas to both users and guests
            foreach ($this->_roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    foreach ($actions as $action){
                        $this->acl->allow($role->getName(), $resource, $action);
                    }
                }
            }
        }
    }
    
    public function beforeDispatch(Event $event, Dispatcher $dispatcher){
        
        if($this->_status){
            $auth = $this->session->get('auth');
            if (!$auth){
                $role = 'Guests';
            }else{
                $role = $auth['role'];
            }
            $controller     = $dispatcher->getControllerName();
            $action         = $dispatcher->getActionName();
            $acl            = $this->getAcl();
            $allowed        = $acl->isAllowed($role, $controller, $action);
            if ($allowed != Acl::ALLOW) {
               
                // Getting a response instance
                $response = new \Phalcon\Http\Response();
                $response->redirect('/user/login', true);
                $response->send();
                
                return false;
            }
        }else{
            return true;
        }
        
    }
    
}