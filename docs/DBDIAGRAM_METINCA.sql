Table departments {
  id int [pk, increment]
  code varchar
  name varchar
}

Table divisions {
  id int [pk, increment]
  code varchar
  name varchar
  department_id int [ref: > departments.id]
}

Table positions {
  id int [pk, increment]
  code varchar
  name varchar
}

Table employees {
  nik varchar [pk]
  name varchar
  email varchar
  department_id int [ref: > departments.id]
  division_id int [ref: > divisions.id]
  position_id int [ref: > positions.id]
}

Table users {
  id int [pk, increment]
  email varchar [unique]
  password varchar
  role varchar
  employee_nik varchar [ref: > employees.nik]
}

Table skills {
  id int [pk, increment]
  code varchar [unique]
  name varchar
  division_id int [ref: > divisions.id]
}

Table employee_competencies {
  id int [pk, increment]
  employee_nik varchar [ref: > employees.nik]
  skill_id int [ref: > skills.id]
  level int
  verified_by int [ref: > users.id]
}

Table division_skills {
  id int [pk, increment]
  division_id int [ref: > divisions.id]
  skill_id int [ref: > skills.id]
  required_level int
  is_mandatory boolean
}

Table exams {
  id int [pk, increment]
  skill_id int [ref: > skills.id]
  title varchar
  passing_score int
  target_level int
}

Table questions {
  id int [pk, increment]
  skill_id int [ref: > skills.id]
  type varchar
  question_text text
  correct_answer varchar
  for_level int
}

Table exam_question {
  exam_id int [ref: > exams.id]
  question_id int [ref: > questions.id]
  
  indexes {
    (exam_id, question_id) [pk]
  }
}

Table exam_sessions {
  id int [pk, increment]
  exam_id int [ref: > exams.id]
  employee_nik varchar [ref: > employees.nik]
  score int
  status varchar
  verified_by int [ref: > users.id]
}

Table exam_answers {
  id int [pk, increment]
  exam_session_id int [ref: > exam_sessions.id]
  question_id int [ref: > questions.id]
  selected_answer varchar
  is_correct boolean
}
