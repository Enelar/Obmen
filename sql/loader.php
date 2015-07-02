<?php

function SQLLoad($file)
{
  header("SQL: $file", false);

  $con = db::RawConnection();
  $sql = file_get_contents($file);
  pg_send_query($con, $sql);
  if (($error = pg_last_error($con)) !== '')
    die("Failure at loading {{$file}} with {{$error}}");

  while (pg_connection_busy($con))
    sleep(1);

  while (pg_get_result($con) !== false)
    if (($error = pg_last_error($con)) !== '')
      die("Failure at loading {{$file}} with {{$error}}");
}

$res = @db::Query("SELECT * FROM users.info WHERE uid=0");
if (pg_last_error() != "")
  SQLLoad("sql/schema.sql");
if (!$res())
{
  SQLLoad("sql/initial.sql");

  // Additional loading
  if (@db::Query("SELECT count(*) FROM public.categories", [], true)->count < 1000)
    SQLLoad("sql/categories.sql");
}

header("SQL: OK", false);