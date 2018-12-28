/// <reference path='./index.ts'/>

class AjaxRequest
{
    protected Caller : IResponseInterpreter;

    public Url : string;
    public Method : string;
    public Async : boolean;
    public Data ?: Object;
    public ContentType ?: string;

    public constructor (
        caller : IResponseInterpreter,
        url : string,
        method : string = "GET",
        async : boolean = true,
        data = undefined,
        contentType = "")
    {
        this.Caller = caller;
        this.Url = url;
        this.Method = method.toUpperCase().trim();
        this.Async = async;
        this.Data = data;
        this.ContentType = contentType;
    }

    public Send()
    {
        let request = new XMLHttpRequest();
        request.open(this.Method, this.Url, this.Async);

        if(this.ContentType.length > 0) {
            request.setRequestHeader("Content-Type", "application/json");
        }

        request.onreadystatechange = (e) => {
            if(request.readyState === request.DONE) {
                if(request.status >= 200 && request.status < 300) {
                    this.Caller.ReceiveSuccess(request.status, request.responseText);
                } else {
                    this.Caller.ReceiveFailure(request.status, request.responseText);
                }
            }
        }
        
        if(this.Data) {
            request.send(this.Data);            
        } else {
            request.send();
        }
        this.Caller.ReceiveBusy();
    }
}