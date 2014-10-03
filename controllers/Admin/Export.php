<?php

import('GradeExporter');

class Export extends Controller {
  public function get() {
    $exporter = new GradeExporter($this->db);
    $this->response->json($exporter->export());
  }
}
