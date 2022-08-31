<?php
/**
 * UserAccount model for the Signifyd SDK
 *
 * PHP version 5.6
 *
 * @category  Signifyd_Fraud_Protection
 * @package   Signifyd\Core
 * @author    Signifyd <info@signifyd.com>
 * @copyright 2018 SIGNIFYD Inc. All rights reserved.
 * @license   See LICENSE.txt for license details.
 * @link      https://www.signifyd.com/
 */
namespace Signifyd\Core\Response;

use Signifyd\Core\Exceptions\LoggerException;
use Signifyd\Core\Response;
use Signifyd\Models\Decision;
use Signifyd\Models\Coverage;

class SaleResponse extends Response
{
    /**
     * Signifyd's unique ID for this event.
     *
     * @var integer
     */
    public $signifydId;

    /**
     * Unique identifier for the order. You should retain the value so you can
     * retrieve summary information about the order later.
     *
     * @var string
     */
    public $orderId;

    /**
     * MetaData about Signifyd's Decision for this event.
     *
     * @var Decision
     */
    public $decision;

    /**
     * Details about the coverage Signifyd will cover on each type of claim.
     *
     * @var Coverage
     */
    public $coverage;

    /**
     * Messages describing the error.
     *
     * @var array of string
     */
    public $messages = [];

    /**
     * If there is an error and you would like to contact Signifyd,
     * please include this id in your support request.
     *
     * @var string
     */
    public $traceId;

    public $errors;

    /**
     * The class attributes
     *
     * @var array $fields The list of class fields
     */
    protected $fields = [
        'signifydId',
        'orderId',
        'decision',
        'coverage',
        'messages',
        'traceId',
    ];

    /**
     * The validation rules
     *
     * @var array $fieldsValidation List of rules
     */
    protected $fieldsValidation = [
        'signifydId' => [],
        'orderId' => [],
        'decision' => [],
        'coverage' => [],
        'messages' => [],
        'traceId' => [],
    ];

    /**
     * UserAccount constructor.
     *
     * @param array $data The user account data
     */
    public function __construct($logger)
    {
        if (!is_object($logger) || get_class($logger) !== 'Signifyd\Core\Logging') {
            throw new LoggerException('Invalid logger parameter');
        }

        $this->logger = $logger;
    }

    /**
     * Validate the user account
     *
     * @return bool
     */
    public function validate()
    {
        $valid = [];

        //TODO add code to validate the user account
        return (!isset($valid[0]))? true : false;
    }

    public function setObject($response)
    {
        $responseArr = json_decode($response, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            $this->addMessage(json_last_error_msg());
            return $this;
        }

        foreach ($responseArr as $field => $value) {
            if (!in_array($field, $this->fields)) {
                continue;
            }

            if ($field == 'decision') {
                if (isset($responseArr['decision'])) {
                    if ($responseArr['decision'] instanceof Decision) {
                        $this->setDecision($responseArr['decision']);
                    } else {
                        $decision = new Decision($responseArr['decision']);
                        $this->setDecision($decision);
                    }
                }
                continue;
            }

            if ($field == 'coverage') {
                if (isset($responseArr['coverage'])) {
                    if ($responseArr['coverage'] instanceof Coverage) {
                        $this->setCoverage($responseArr['coverage']);
                    } else {
                        $coverage = new Coverage($responseArr['coverage']);
                        $this->setCoverage($coverage);
                    }
                }
                continue;
            }

            if ($field == 'messages') {
                foreach ($value as $item) {
                    $this->addMessage($item);
                }
                continue;
            }

            $this->{'set' . ucfirst($field)}($value);
        }

        return true;
    }

    /**
     * Set the error
     *
     * @param int    $httpCode The response code
     * @param string $error    The response
     *
     * @return void
     */
    public function setError($httpCode, $error)
    {
        $this->addMessage($error);
    }

    public function getSignifydId()
    {
        return $this->signifydId;
    }

    public function setSignifydId($signifydId)
    {
        $this->signifydId = $signifydId;
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    public function getDecision()
    {
        return $this->decision;
    }

    public function setDecision($decision)
    {
        $this->decision = $decision;
    }

    public function getCoverage()
    {
        return $this->coverage;
    }

    public function setCoverage($coverage)
    {
        $this->coverage = $coverage;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    public function getTraceId()
    {
        return $this->traceId;
    }

    public function setTraceId($traceId)
    {
        $this->traceId = $traceId;
    }
}