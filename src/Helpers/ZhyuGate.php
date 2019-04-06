<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2019-03-26
 * Time: 13:26
 */

namespace Zhyu\Helpers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Zhyu\Repositories\Eloquents\ResourceRepository;
use Zhyu\Repositories\Eloquents\UsergroupPermissionRepository;
use Zhyu\Repositories\Eloquents\UserPermissionRepository;
use Illuminate\Support\Facades\Log;

class ZhyuGate
{
	private $resourceRepository;
	private $userPermission;
	private $usergroupPermission;
	
	public function __construct(ResourceRepository $resourceRepository, UserPermissionRepository $userPermission, UsergroupPermissionRepository $usergroupPermission)
	{
		$this->resourceRepository = $resourceRepository;
		$this->userPermission = $userPermission;
		$this->usergroupPermission = $usergroupPermission;
	}
	
	public function init()
	{
		Gate::before(function($user, $ability){
			$user_ids = explode(',', env('ZHYU_ADMIN_USER_IDS'));
			if(is_array($user_ids) && in_array($user->id, $user_ids)){
				
				return true;
			}
		});
		
		
		if(Schema::hasTable('resources') && Schema::hasTable('user_permissions') && Schema::hasTable('usergroup_permissions')) {
			$resources = $this->resourceRepository->findWhereCache([
				['parent_id', '>', '0']
			], ['*'], env('APP_TYPE').'ZhyuParentResources', now()->addMinutes(60));
			
			$userPermissions = $this->userPermission->allCache(['*'], env('APP_TYPE').'ZhyuAllUserPermissions', now()->addMinutes(60));
			$usergroupPermissions = $this->usergroupPermission->all(['*'], env('APP_TYPE').'ZhyuAllUsergroupPermissions', now()->addMinutes(60));
			
			foreach ($resources as $resource) {
				$parent_route_name = $resource->parent->route;
				$parent_route = strlen($parent_route_name) > 0 ? $parent_route_name . '.' : '';
				
				$permissions = $userPermissions->where('resource_id', $resource->id);
				if ($permissions->count() > 0) {
					$permissions->map(function ($permission) use ($resource, $parent_route, $userPermissions, $usergroupPermissions) {
						$name = $this->resolveName($parent_route, $resource->route, $permission->act);
						
						Gate::define($name, function ($user) use ($resource, $permission, $userPermissions, $usergroupPermissions) {
							$user_pers = $userPermissions->where('user_id', $user->id);
							if ($user_pers->count() > 0) {
								
								return $user_pers->where('resource_id', $resource->id)->where('act', $permission->act)->count() > 0 ? true : false;
							} else {
								$usergroup_pers = $usergroupPermissions->where('usergroup_id', $user->usergroup->id);
								
								return $usergroup_pers->where('resource_id', $resource->id)->where('act', $permission->act)->count() > 0 ? true : false;
							}
						});
					});
				}
				
				$permissions = $usergroupPermissions->where('resource_id', $resource->id);
				if ($permissions->count() > 0) {
					$permissions->map(function ($permission) use ($resource, $parent_route, $usergroupPermissions) {
						$name = $this->resolveName($parent_route, $resource->route, $permission->act);
						//Log::info('route: '.$name);
						Gate::define($name, function ($user) use ($resource, $permission, $usergroupPermissions) {
							$usergroup_pers = $usergroupPermissions->where('usergroup_id', $user->usergroup->id);
							
							return $usergroup_pers->where('resource_id', $resource->id)->where('act', $permission->act)->count() > 0 ? true : false;
						});
					});
				}
			}
		}
	}
	
	private function resolveName($parent_route, $resource_route, $act){
		if(strlen($resource_route)>0) {
			
			return $parent_route.$resource_route.'.'.$act;
		}else{
			
			return $parent_route.$act;
		}
	}
}