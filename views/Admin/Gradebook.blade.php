<table class="grid" style="width: auto !important;">
  <thead>
    <tr>
      <th width="300">
        Student
      </th>
      <th width="100">
        Section
      </th>
      <th width="100">
        Net ID
      </th>
      <th width="100">
        Table
      </th>
      @foreach($assignments as $a)
      <th width="70">
       {{{ $a['month'] }}}/{{{ $a['day'] }}}/{{{ $a['year'] }}}
      </th>
      @endforeach
    </tr>
  </thead>
  <tbody>
    @foreach($students as $student)
      <tr>
        <td>{{{ $student['last_name'] }}}, {{{ $student['first_name'] }}}</td>
        <td>{{{ $student['section'] }}}</td>
        <td>{{{ $student['netid'] }}}</td>
        <td>{{{ $student['table'] }}}</td>
        @foreach($assignments as $a)
          @if(isset($grades[$a['id']][$student['id']]))
            <td>{{{ GradingSystem::formatGrade($grades[$a['id']][$student['id']]) }}}</td>
          @else
            <td>-</td>
          @endif
        @endforeach
      </tr>
    @endforeach
  </tbody>
</table>
