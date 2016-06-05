<?php

namespace AppBundle\Event;

class Event
{
    const DIRECTORY_PRE_MOVE = 'directory_pre_move';
    const DIRECTORY_POST_MOVE = 'directory_post_move';
    const DIRECTORY_PRE_DELETE = 'directory_pre_delete';
    const DIRECTORY_POST_DELETE = 'directory_post_delete';
}