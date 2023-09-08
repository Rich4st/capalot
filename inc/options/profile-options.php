<?php

defined('ABSPATH') || exit;

if (!is_admin())
  exit;

if (!class_exists('CSF'))
  exit;

$prefix = _OPTIONS_PREFIX . '-profile';

CSF::createProfileOptions($prefix, array(
  'data_type' => 'unserialize',
));

CSF::createSection($prefix, array(
  'title' => '用户高级信息',
  'fields' => array(

    array(
      'id' => 'capalot_user_type',
      'type' => 'select',
      'title' => '用户VIP会员类型',
      'options' => array(
        'no' => '普通用户',
        'vip' => '包月VIP',
      ),
      'default' => 'no',
    ),

    array(
      'id'      => 'cao_ref_from',
      'type'    => 'text',
      'title'   => '推荐人ID',
      'default' => '',
    ),

  )
));
