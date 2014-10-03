<?php

import('GradingSystem');

class GradeExporter {
  protected /*Database*/ $db;

  public /*void*/ function __construct(Database &$db) {
    $this->db =& $db;
  }

  public /*ArrayObject<string, double>*/ function export() {
    $students = GradingSystem::getAllStudents();
    $assignments = GradingSystem::getAllAssignments();
    $map = array();

    foreach ($students as $student) {
      $map[$student['netid']] = $this->calculateGrade($student, $assignments);
    }

    return $map;
  }

  public /*double*/ function calculateGrade(/*ArrayAccess<string, string>*/ $student,
                                            /*Iterator<ArrayAccess<string, string>>*/ $assignments) {
    $total = 0;
    $points = 0.0;
    $grades = GradingSystem::getAllGrades((int) $student['id']);

    foreach ($assignments as $assignment) {
      if ((int) $assignment['section'] != (int) $student['section']) {
        continue;
      }

      // If the student received no grade, assume absent.
      $grade = isset($grades[(int) $assignment['id']]) ?
          $grades[(int) $assignment['id']] :
          Grades::Absent;

      // Update total and points based upon the grade.
      switch ($grade) {
        case Grades::Absent:
          $total++;
          $points += 0;
          break;

        case Grades::Late:
          $total++;
          $points += 1;
          break;

        case Grades::CheckMinus:
          $total++;
          $points += 2;
          break;

        case Grades::Check:
          $total++;
          $points += 3;
          break;

        case Grades::CheckPlus:
          $total++;
          $points += 4;
          break;

        case Grades::ExcusedAbsence:
          // No impact on grade.
          break;

        default:
          throw new Exception("Unrecognized grade: " . $grade);
      }
    }

    // The average points earned per day will determine the student's grade.
    return ($total == 0) ? 0 : $points / $total;
  }
}
