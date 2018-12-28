/// <reference path='./index.ts'/>
/// <reference path='../RequestStation/index.ts'/>
/// <reference path='../ResponseInterpreter/index.ts'/>

abstract class RequestingAction implements IResponseInterpreter, Action
{
    protected SubInterpreters : IResponseInterpreter[];
    
    public constructor()
    {
        this.SubInterpreters = [];
    }
    
    public abstract Invoke();

    public Attach(irp : IResponseInterpreter)
    {
        this.SubInterpreters.push(irp);
    }
    public Detach(irp : IResponseInterpreter)
    {
        let index = this.SubInterpreters.indexOf(irp);
        if(index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    }

    protected abstract PerformSuccess(code:number,responseText:string);
    protected abstract PerformFailure(code:number,responseText:string)
    protected abstract PerformBusy();

    public ReceiveBusy() 
    {
        this.PerformBusy();
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    }
    public ReceiveSuccess(code: number, responseText: string)
    {
        this.PerformSuccess(code, responseText);
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveSuccess(code, responseText);
        }
    }
    public ReceiveFailure(code: number, responseText: string)
    {
        this.PerformFailure(code, responseText);
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveFailure(code, responseText);
        }
    }
}