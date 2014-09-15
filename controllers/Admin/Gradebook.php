<?php

class Gradebook extends Controller {
  public function get() {
    $content = "";

    foreach(GradingConfig::$section as $section => $sname) {
      $view = $this->make_view($section);
      $content .= $view->render();
    }

    return View::make('Composite')->with(['content' => $content]);
  }

  public function make_view($section) {
    $assignments = GradingSystem::getAllAssignmentsInSection($section);
    $grades = array();

    foreach($assignments as $assignment) {
      $grades[$assignment['id']] = GradingSystem::getGrades($assignment['id']);
    }

    return View::make('Admin.Gradebook')->with(array(
      'students' => GradingSystem::getAllStudentsInSection($section),
      'assignments' => $assignments,
      'grades' => $grades
    ));
  }
}
