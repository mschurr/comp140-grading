<?php

class Assignments extends Controller
{
	public function get()
	{
		try {
			$paginator = GradingSystem::getAllAssignments();

			$page = 0;
			if(isset($this->request->get['page'])) {
				if(!ctype_digit($this->request->get['page']))
					return 404;
				$page = ((int) $this->request->get['page']) - 1;
			}

			return View::make('Admin.AssignmentList')->with(array(
				'assignments' => $paginator[$page],
				'pages' => count($paginator),
				'page' => $page + 1,
				'pageUrl' => function($page) {
					return URL::to(array($this, 'get'))->with(array('page' => $page));
				}
			));
		} catch (BadAccessException $e) {
			return 404;
		}
	}

	public function add()
	{
		throw new NotImplementedException();
	}

	public function edit($id)
	{
		throw new NotImplementedException();
	}

	public function delete($id)
	{
		throw new NotImplementedException();
	}

	public function addAction()
	{
		throw new NotImplementedException();
	}

	public function editAction($id)
	{
		throw new NotImplementedException();
	}

	public function deleteAction($id)
	{
		throw new NotImplementedException();
	}
}