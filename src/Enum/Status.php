<?php

namespace Poweroffice\Enum;

enum Status: int
{
    case OK = 200;
    case RESOURCE_CREATED = 201;
    case NO_CONTENT = 204;

    case INVALID_REQUEST = 400;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case TO_MANY_REQUESTS = 429;
}