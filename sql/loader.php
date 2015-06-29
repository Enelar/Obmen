<?php

function SQLLoad($file)
{
  header("SQL: $file", false);

  $sql = file_get_contents($file);
  pg_send_query(db::RawConnection(), $sql);
  if (($error = pg_last_error()) !== '')
    die("Failure at loading {{$file}} with {{$error}}");
  while (pg_get_result() !== false)
    if (($error = pg_last_error()) !== '')
      die("Failure at loading {{$file}} with {{$error}}");
}

$res = db::Query("SELECT * FROM users.info WHERE uid=0");
if (pg_last_error() != "")
  SQLLoad("sql/schema.sql");
if (!$res())
  SQLLoad("sql/initial.sql");

header("SQL: OK", false);