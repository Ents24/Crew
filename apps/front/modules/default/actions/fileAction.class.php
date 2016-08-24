<?php

/**
 * repository actions.
 *
 * @package    crew
 * @subpackage repository
 * @author     Your name here
 */
class fileAction extends crewAction
{
  /**
   * @param sfWebRequest $request
   * @return void
   */
  public function execute($request)
  {
    $this->file = FilePeer::retrieveByPK($request->getParameter('file'));
    $this->forward404Unless($this->file, "File not found");
    $this->getResponse()->setTitle(basename($this->file->getFilename()));

    $this->branch = BranchPeer::retrieveByPK($this->file->getBranchId());
    $this->forward404Unless($this->branch, "Branch not found");

    $this->repository = RepositoryPeer::retrieveByPK($this->branch->getRepositoryId());
    $this->forward404Unless($this->repository, "Repository not found");

    $options = array();
    if ($request->getParameter('s', false))
    {
      $options['ignore-all-space'] = true;
    }

    // use the same list as fileListAction.class.php
    $files = FileQuery::create()
      ->filterByBranchId($this->file->getBranchId())
      ->find()
    ;

    $commitFrom        = $request->getParameter('from', $this->branch->getCommitReference());
    $commitTo          = $request->getParameter('to', $this->branch->getLastCommit());
    $this->commit_from = null;
    $this->commit_to   = null;
    $this->readonly    = false;
    if ($request->hasParameter('from'))
    {
      $this->commit_from = $commitFrom;
      $this->readonly    = true;
    }

    if ($request->hasParameter('to'))
    {
      $this->commit_to = $commitTo;
      $this->readonly  = true;
    }

    $modifiedFiles = $this->gitCommand->getDiffFilesFromBranch(
      $this->repository->getGitDir(),
      $commitFrom,
      $commitTo,
      false
    );
    
    $lastId = $this->previousFileId = $this->nextFileId = null;
    $getNext = false;
    // iterate on the list only once and get both previous and next ids
    foreach ($files as $file)
    {
      /** @var File $file  */
      if (!isset($modifiedFiles[$file->getFilename()]))
      {
        continue;
      }
      if ($getNext) {
        // save the current id as next id
        $this->nextFileId = $file->getId();
        break;
      }
      if ($this->file->getId() == $file->getId()) {
        // save the last valid file's id as previous id
        $this->previousFileId = $lastId;
        // set flag so next iteration with a valid file will know what to do
        $getNext = true;
      }
      $lastId = $file->getId();
    }

    $this->fileContentLines = $this->gitCommand->getShowFileFromBranch(
      $this->repository->getGitDir(),
      $commitFrom,
      $commitTo,
      $this->file->getFilename(),
      $options
    );

    $fileLineCommentsModel = CommentQuery::create()
      ->filterByFileId($this->file->getId())
      ->filterByCommit($this->file->getLastChangeCommit())
      ->filterByType(CommentPeer::TYPE_LINE)
      ->find()
    ;

    $this->userId = $this->getUser()->getId();

    $this->fileLineComments = array();
    foreach ($fileLineCommentsModel as $fileLineCommentModel)
    {
      $this->fileLineComments[$fileLineCommentModel->getPosition()][] = $fileLineCommentModel;
    }
  }

}
