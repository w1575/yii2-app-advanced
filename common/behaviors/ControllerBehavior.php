<?php


namespace common\behaviors;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;


class ControllerBehavior extends \yii\base\Behavior
{
    /**
     * Кидает ошибку
     * @param string $message  сообщение ошибки
     * @throws ForbiddenHttpException
     */
    public function throw403(string $message = "У Вас нет доступа к данной странице")
    {
        throw new ForbiddenHttpException($message);
    }

    /**
     * Кидает ошибку
     * @param string $message сообщение ошибки
     * @throws NotFoundHttpException
     */
    public function throw404(string $message = "Страница не найдена")
    {
        throw new  NotFoundHttpException($message);
    }

    /**
     * Небольшая надстройка над проверкой разрегения. Сделал только для того, чтобы каждый раз не
     * писать Yii::$app->user->can($permission, $params)
     * @param string $permission
     * @param array $params
     * @param bool $throwError
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function userCan(string $permission, array $params = [], bool $throwError = true)
    {
        if (Yii::$app->user->can($permission, $params) === false) {
            if ($throwError === true) {
                $this->throw403();
            } else {
                return false;
            }
        }

        return true;
    }
}