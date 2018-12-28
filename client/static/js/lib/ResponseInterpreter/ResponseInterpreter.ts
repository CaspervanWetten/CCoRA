interface IResponseInterpreter
{
    ReceiveBusy();
    ReceiveSuccess(code : number, responseText : string);    
    ReceiveFailure(code : number, responseText : string);

    Attach(irp : IResponseInterpreter);
    Detach(irp : IResponseInterpreter);
}