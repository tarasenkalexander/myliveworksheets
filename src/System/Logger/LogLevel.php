<?php

namespace System\Logger;

enum LogLevel {
    case Critical;
    case Error;
    case Warning;
    case Debug;
    case Info;
}