<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserAnswer;

class AnswerResult extends Model
{
    use HasFactory;

    private $percentageText;
    private $totalCorrect;
    private $messageText;
    private $user_answer;

    /**
     * @param $percentageText
     * @param $totalCorrect
     * @param $messageText
     * @param $user_answer
     */
    public function __construct($percentageText, $totalCorrect, $messageText,UserAnswer $user_answer)
    {
        $this->percentageText = $percentageText;
        $this->totalCorrect = $totalCorrect;
        $this->messageText = $messageText;
        $this->user_answer = $user_answer;
    }


    /**
     * @return mixed
     */
    public function getPercentageText()
    {
        return $this->percentageText;
    }

    /**
     * @param mixed $percentageText
     */
    public function setPercentageText($percentageText): void
    {
        $this->percentageText = $percentageText;
    }

    /**
     * @return mixed
     */
    public function getTotalCorrect()
    {
        return $this->totalCorrect;
    }

    /**
     * @param mixed $totalCorrect
     */
    public function setTotalCorrect($totalCorrect): void
    {
        $this->totalCorrect = $totalCorrect;
    }

    /**
     * @return mixed
     */
    public function getMessageText()
    {
        return $this->messageText;
    }

    /**
     * @param mixed $messageText
     */
    public function setMessageText($messageText): void
    {
        $this->messageText = $messageText;
    }

    /**
     * @return mixed
     */
    public function getUserAnswer()
    {
        return $this->user_answer;
    }

    /**
     * @param mixed $user_answer
     */
    public function setUserAnswer(UserAnswer $user_answer): void
    {
        $this->user_answer = $user_answer;
    }


}
