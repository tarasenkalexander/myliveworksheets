<?php

/**
 * единая точка входа, шлёт в контроллер главной страницы
 * 
 */


 

/**
 * 
 * Вопросы по реализации:
 * 
 * Как разделить страницу на "для зарегистрированных и для незарегистированных"?
 * Как сделать прохождение задания?
 * 
 * Сначала сделать загрузку файла на фронт
 * Потом загрузку файла на бэк
 * Потом создание задания на фронте
 * Потом сохранение задания на бэке
 * 
 * И только потом регистрация и всё остальное
 * 
 * Нужны ли приписки v_ шаблонам?
 * Использовать twig или базовый template?
 * 
 */