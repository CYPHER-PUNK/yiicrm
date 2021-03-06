<?php

/**
 * This is the model class for table "{{payment_money}}".
 *
 * The followings are the available columns in table '{{payment_money}}':
 * @property integer $id
 * @property integer $type
 * @property integer $method
 * @property integer $payment_id
 * @property string $date
 * @property integer $amount
 * @property integer $create_user_id
 * @property integer $update_user_id
 * @property string $create_time
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property Payment $payment
 * @property User $createUser
 * @property User $updateUser
 *
 * @property StatusBehavior $statusMethod
 * @property StatusBehavior $statusType
 */
class PaymentMoney extends CActiveRecord
{
    const TYPE_PARTNER = 0;
    const TYPE_AGENT = 1;
    /**
     * @var int
     * @see Client::id
     */
    public $clientId;

    /**
     * @var int
     * @see Payment::partner_id
     */
    public $paymentPartnerId;

    /**
     * @var string
     * @see Client::name_company
     */
    public $nameCompany;

    /**
     * @var string
     * @see Client::name_contact
     */
    public $nameContact;

    /**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return PaymentMoney the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{payment_money}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('payment_id, amount', 'required'),
            array('type, method, payment_id, amount, create_user_id, update_user_id, clientId, paymentPartnerId', 'numerical', 'integerOnly' => true),
            array('date', 'type', 'type' => 'datetime', 'datetimeFormat' => 'yyyy-MM-dd hh:mm:ss'),
            // The following rule is used by search().
            array('id, type, method, payment_id, date, amount, create_user_id, update_user_id, create_time, update_time, nameCompany, nameContact', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Returns a list of behaviors that this model should behave as.
     * @return array the behavior configurations (behavior name=>behavior configuration)
     */
    public function behaviors()
    {
        return array(
            'SaveBehavior' => array(
                'class' => 'application.components.behaviors.SaveBehavior',
            ),
            'statusType'   => array(
                'class'     => 'application.components.behaviors.StatusBehavior',
                'attribute' => 'method',
                'list'      => array('Партнеру', 'Агенту')
            ),
            'statusMethod' => array(
                'class' => 'StatusBehavior',
                'list'  => array('Наличные', 'Р/С', 'Карта')
            ),
            'ERememberFiltersBehavior' => array(
                'class' => 'application.components.behaviors.ERememberFiltersBehavior',
                //'defaults'=>array(),           /* optional line */
                //'defaultStickOnClear'=>false   /* optional line */
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'payment' => array(self::BELONGS_TO, 'Payment', 'payment_id'),
            'createUser' => array(self::BELONGS_TO, 'User', 'create_user_id'),
            'updateUser' => array(self::BELONGS_TO, 'User', 'update_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'type' => Yii::t('CrmModule.paymentMoney', 'Type'),
            'method' => Yii::t('CrmModule.paymentMoney', 'Payment Method'),
            'payment_id' => Yii::t('CrmModule.paymentMoney', 'Payment'),
            'date' => Yii::t('CrmModule.paymentMoney', 'Date'),
            'amount' => Yii::t('CrmModule.paymentMoney', 'Amount'),
            'create_user_id' => Yii::t('CrmModule.paymentMoney', 'Create User'),
            'update_user_id' => Yii::t('CrmModule.paymentMoney', 'Update User'),
            'create_time' => Yii::t('CrmModule.paymentMoney', 'Create Time'),
            'update_time' => Yii::t('CrmModule.paymentMoney', 'Update Time'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        $criteria->with = array(
            'payment' => array('select' => 'client_id, name_company, name_contact'),
            'payment.client' => array('select' => 'client_id')
        );

		$criteria->compare('id', $this->id);
		$criteria->compare('type', $this->type);
		$criteria->compare('payment_id', $this->payment_id);
        if (strlen($this->date) > 10) {
            $this->date = trim($this->date);
            $from       = substr($this->date, 0, 10);
            $to         = date('Y-m-d', strtotime('+1 day', strtotime(substr($this->date, -10))));
            if ($from != substr($this->date, -10)) {
                $criteria->addBetweenCondition('date', $from, $to);
            } else {
                $criteria->compare('date', $from, true);
            }
        } else {
            $criteria->compare('date', $this->date, true);
        }
		//$criteria->compare('date', $this->date, true);
		$criteria->compare('amount', $this->amount);
		$criteria->compare('create_user_id', $this->create_user_id);
		$criteria->compare('update_user_id', $this->update_user_id);
		$criteria->compare('create_time', $this->create_time, true);
		$criteria->compare('update_time', $this->update_time, true);
        $criteria->compare('payment.partner_id', $this->paymentPartnerId);
        $criteria->compare('payment.client_id', $this->clientId);
        $criteria->compare('payment.name_company', $this->nameCompany);
        $criteria->compare('payment.name_contact', $this->nameContact);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 50,
            ),
            'sort'       => array(
                'defaultOrder' => array('date' => true),
                'attributes'   => array(
                    'payment.client_id' => array(
                        'asc'  => 'client_id',
                        'desc' => 'client_id DESC'
                    ),
                    'payment.name_company' => array(
                        'asc'  => 'payment.name_company',
                        'desc' => 'payment.name_company DESC'
                    ),
                    'payment.name_contact' => array(
                        'asc'  => 'payment.name_contact',
                        'desc' => 'payment.name_contact DESC'
                    ),
                    '*'
                )
            )
        ));
    }

    protected function beforeValidate()
    {
        if ($date = CDateTimeParser::parse($this->date, 'yyyy-MM-dd', array('hour' => date('H'), 'minute'))) {
            $this->date = date('Y-m-d H:i:s', $date);
        }
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->date = substr($this->date, 0, 10);
        parent::afterFind();
    }

    public function afterSave()
    {
        $this->payment->save();
        parent::afterSave();
    }
}