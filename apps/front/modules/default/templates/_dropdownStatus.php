<?php /** @var int $status */ ?>
<?php /** @var string $type */ ?>
<?php /** @var int $id */ ?>
<?php /** @var boolean $readonly */ ?>
<?php
if ($type == "branch") {
  $labelByStatus = array(
    BranchPeer::ONIT      => 'L',
  );
} else {
  $labelByStatus = array(
    BranchPeer::REFACT    => 'Å',
  );
}

$labelByStatus += array(
  BranchPeer::OK        => 'Ã',
  BranchPeer::KO        => 'Â',
  BranchPeer::A_TRAITER => '!',
);
$cssByStatus   = array(
  BranchPeer::OK        => 'validate',
  BranchPeer::KO        => 'invalidate',
  BranchPeer::A_TRAITER => 'todo',
  BranchPeer::ONIT      => 'todo',
  BranchPeer::REFACT    => 'refactor',
);
$titleByStatus = array(
  BranchPeer::OK        => 'Validated',
  BranchPeer::KO        => 'Invalidated',
  BranchPeer::A_TRAITER => 'Todo',
  BranchPeer::ONIT      => "I'm on it",
  BranchPeer::REFACT    => "Refactor needed",
); 
?>
<?php if ($readonly): ?>
  <ul class="right dropdown-status">
    <li class="dropdown" >
      <span class="dropdown-toggle ricon <?php echo  $cssByStatus[$status] ?>" title="<?php echo $titleByStatus[$status]; ?>">
        <?php echo $labelByStatus[$status] ?>
      </span>
    </li>
  </ul>
<?php else: ?>
  <?php
  $url        = 'default/changeStatus';
  $parameters = array(
      'type' => $type,
      'id'   => $id
  ); 
  ?>
  <ul class="right dropdown-status dropdown-action">
    <li class="dropdown">
      <?php echo link_to($labelByStatus[$status], $url, array('query_string' => http_build_query($parameters + array('status' => $status)), 'class' => $cssByStatus[$status] . ' dropdown-toggle ricon', 'title' => $titleByStatus[$status])); ?>
      <ul class="dropdown-menu">
      <?php foreach (array_keys($labelByStatus) as $otherStatus): ?>
        <?php if ($otherStatus == $status): continue; endif;?>
        <li><?php echo link_to($labelByStatus[$otherStatus], $url, array('query_string' =>  http_build_query($parameters + array('status' => $otherStatus)), 'class' => $cssByStatus[$otherStatus] . ' ricon item-status-action', 'title' => $titleByStatus[$otherStatus])); ?></li>
      <?php endforeach; ?>
      </ul>
    </li>
  </ul>
<?php endif; ?>