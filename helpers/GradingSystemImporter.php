<?php

class GradingSystemImporterException extends Exception {}

class GradingSystemImporter {
  private function getFileJson($path) {
    try {
      $json = from_json(File::open($path)->content);

      if ($json === null) {
        throw new GradingSystemImporterException("Failed to read JSON from file " . $path);
      }

      return $json;
    } catch (FileException $e) {
      throw new GradingSystemImporterException($e);
    }
  }

  public function importStudentsFromFile($path) {
    $this->importStudents($this->getFileJson($path));
  }

  public function importGradersFromFile($path) {
    $this->importGraders($this->getFileJson($path));
  }

  public function importGraderAssignmentsFromFile($path) {
    $this->importGraderAssignments($this->getFileJson($path));
  }

  public function importInstructorsFromFile($path) {
    $this->importInstructors($this->getFileJson($path));
  }

  public function importStudents($json) {
    foreach($json as $data) {
      GradingSystem::addStudent(
        $data['netid'],
        $data['email'],
        $data['last_name'],
        $data['first_name'],
        $data['section'],
        $data['table']
      );
    }
  }

  public function importGraders($json) {
    foreach ($json as $netid => $data) {
      $user = GradingSystem::enforceExistence($netid);
      GradingSystem::addGrader($netid);

      if (isset($data['name'])) {
        $user->setProperty('name', $data['name']);
      }
    }
  }

  public function importInstructors($json) {
    foreach ($json as $netid => $data) {
      $user = GradingSystem::enforceExistence($netid);
      GradingSystem::addInstructor($netid);

      if (isset($data['name'])) {
        $user->setProperty('name', $data['name']);
      }
    }
  }

  public function importGraderAssignments($json) {
    foreach ($json as $student_netid => $grader_netid) {
      $grader = GradingSystem::enforceExistence($grader_netid);

      if (!$grader->hasPrivilege(Privilege::TeachingAssistant)) {
        fprintf(STDOUT, "WARNING: %s is not marked as a grader; skipping...\n", $grader_netid);
        continue;
      }

      $student = GradingSystem::getStudentByNetId($student_netid);

      if (!$student) {
        fprintf(STDOUT, "WARNING: %s is not a valid student; skipping...\n", $student_netid);
        continue;
      }

      try {
        GradingSystem::assignGrader($grader->id(), $student['id']);
      } catch (DatabaseException $e) {
        fprintf(STDOUT, "NOTICE: %s already has a grader, replacing...\n", $student_netid);
        GradingSystem::assignGrader($grader->id(), $student['id'], true);
        continue;
      }
    }
  }
}
