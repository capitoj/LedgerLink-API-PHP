<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Factory;

/**
 * Description of LoanIssueFactory
 *
 * @author JCapito
 */

use App\Model\LoanIssue;
use App\Repository\MemberRepo;
use App\Repository\MeetingRepo;
use App\Repository\VslaCycleRepo;
use App\Repository\LoanIssueRepo;

class LoanIssueFactory {
    //put your code here
    protected $loanIssueInfo;
    protected $meetingInfo;
    protected $db;
    
    protected function __construct($db, $loanIssueInfo, $meetingInfo){
        $this->loanIssueInfo = $loanIssueInfo;
        $this->meetingInfo = $meetingInfo;
        $this->db = $db;
    }
    
    protected function __process($targetVsla){
        if(is_array($this->loanIssueInfo)){
            $index = 0;
            for($i = 0; $i < count($this->loanIssueInfo); $i++){
                $loanIssueData = $this->loanIssueInfo[$i];
                $loanIssue = new LoanIssue();
                if(array_key_exists("MemberId", $loanIssueData)){
                    $memberId = MemberRepo::getIDByMemberIdEx($this->db, $targetVsla->getID(), $loanIssueData["MemberId"]);
                    if($memberId != null){
                        
                        $member = (new MemberRepo($this->db, $memberId))->getMember();
                        $loanIssue->setMember($member);
                        
                        if(array_key_exists("LoanId", $loanIssueData)){
                            $loanIssue->setLoanIdEx($loanIssueData["LoanId"]);
                        }
                        if(array_key_exists("PrincipalAmount", $loanIssueData)){
                            $loanIssue->setPrincipalAmount($loanIssueData["PrincipalAmount"]);
                        }
                        if(array_key_exists("InterestAmount", $loanIssueData)){
                            $loanIssue->setInterestAmount($loanIssueData["InterestAmount"]);
                        }
                        if(array_key_exists("TotalRepaid", $loanIssueData)){
                            $loanIssue->setTotalRepaid($loanIssueData["TotalRepaid"]);
                        }
                        if(array_key_exists("LoanBalance", $loanIssueData)){
                            $loanIssue->setBalance($loanIssueData["LoanBalance"]);
                        }
                        if(array_key_exists("DateDue", $loanIssueData)){
                            $loanIssue->setDueDate($loanIssueData["DateDue"]);
                        }
                        if(array_key_exists("Comments", $loanIssueData)){
                            $loanIssue->setComments($loanIssueData["Comments"]);
                        }
                        if(array_key_exists("DateCleared", $loanIssueData)){
                            $loanIssue->setDateCleared($loanIssueData["DateCleared"]);
                        }
                        if(array_key_exists("IsCleared", $loanIssueData)){
                            if($loanIssueData["IsCleared"]){
                                $loanIssue->setIsCleared(1);
                            }else{
                                $loanIssue->setIsCleared(0);
                            }
                        }
                        if(array_key_exists("IsDefaulted", $loanIssueData)){
                            if($loanIssueData["IsDefaulted"]){
                                $loanIssue->setIsDefaulted(1);
                            }else{
                                $loanIssue->setIsDefaulted(0);
                            }
                        }
                        if(array_key_exists("IsWrittenOff", $loanIssueData)){
                            if($loanIssueData["IsWrittenOff"]){
                                $loanIssue->setIsWrittenOff(1);
                            }else{
                                $loanIssue->setIsWrittenOff(0);
                            }
                        }
                        if(array_key_exists("MeetingId", $this->meetingInfo) && array_key_exists("CycleId", $this->meetingInfo)){
                            $vslaCycleId = VslaCycleRepo::getIDByCycleIdEx($this->db, $targetVsla->getID(), $this->meetingInfo["CycleId"]);
                            if($vslaCycleId != null){
                                $meetingId = MeetingRepo::getIDByMeetingIDEx($this->db, $vslaCycleId, $this->meetingInfo["MeetingId"]);
                                $meeting = (new MeetingRepo($this->db, $meetingId))->getMeeting();
                                $loanIssue->setMeeting($meeting);
                            }
                        }
                        if(LoanIssueRepo::save($this->db, $loanIssue) > -1){
                            $index++;
                        }
                    }
                }
            }
            if($index > 0){
                return 1;
            }
        }
        return 0;
    }
    
    public static function process($db, $loanIssueInfo, $meetingInfo, $targetVsla){
        return (new LoanIssueFactory($db, $loanIssueInfo, $meetingInfo))->__process($targetVsla);
    }
}
