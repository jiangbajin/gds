<?php
/**
 * Created by PhpStorm.
 * User: 江艺勤
 * Date: 2019/4/17
 * Time: 17:29
 */

namespace cncn\gds\gateways;


class GatewayServiceType
{
    //这边目前定义的全是票付通专用
    const PFT_GET_SCENIC_SPOT_LIST = 'getScenicSpotList'; //查询景区列表：Get_ScenicSpot_List
    const PFT_GET_SCENIC_SPOT_INFO = 'getScenicSpotInfo'; //查询景区详情信息：Get_ScenicSpot_Info
    const PFT_GET_TICKET_LIST = 'getTicketList'; //查询门票列表：Get_Ticket_List
    const PFT_GET_REAL_TIME_STORAGE = 'getRealTimeStorage'; //动态价格，实时库存上限获取：GetRealTimeStorage
    const PFT_CHECK_PERSON_ID = 'checkPersonID'; //身份证校验接口：Check_PersonID
    const PFT_ORDER_PRE_CHECK = 'orderPreCheck'; //预判下单：OrderPreCheck
    const PFT_PFT_ORDER_SUBMIT = 'pFTOrderSubmit'; //提交订单：PFT_Order_Submit
    const PFT_ORDER_QUERY = 'orderQuery'; //查询订单：OrderQuery
    const PFT_ORDER_CHANGE_PRO = 'orderChangePro'; //修改/取消订单：Order_Change_Pro
    const PFT_RESEND_SMS_GLOBAL_PL = 'reSendSMSGlobalPL'; //订单短信重发接口：reSend_SMS_Global_PL
    const PFT_TERMINAL_CODE_VERIFY = 'terminalCodeVerify'; //查看订单凭证码：Terminal_Code_Verify
    const PFT_GET_SCREENINGS_LIST = 'getScreeningsList'; //获取场次信息接口：Get_Screenings_List
    const PFT_PFT_MEMBER_FUND = 'pFTMemberFund'; //资金余额查看：PFT_Member_Fund
    const PFT_PFT_MEMBER_RELATIONSHIP = 'pFTMemberRelationship'; //会员关系查看:PFT_Member_Relationship

    //其他的如果要求补充，请自定义并且写好注释
}