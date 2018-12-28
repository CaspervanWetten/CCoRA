/// <reference path='./index.ts'/>
abstract class InitRequestingAction extends InitAction implements IResponseInterpreter {
    protected SubInterpreters : IResponseInterpreter[];
    
    public constructor(w : InitWorkspace)
    {
        super(w);
        this.SubInterpreters = [];
    }

    ReceiveBusy() {
        this.PerformBusy();
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    }
    ReceiveSuccess(code: number, responseText: string) {
        this.PerformSuccess(code, responseText);
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    }
    ReceiveFailure(code: number, responseText: string) {
        this.PerformFailure(code, responseText);
        let subs = this.SubInterpreters;
        for(let i = 0; i < subs.length; i++) {
            subs[i].ReceiveBusy();
        }
    }
    
    protected abstract PerformBusy();
    protected abstract PerformSuccess(code:number, responseText:string);
    protected abstract PerformFailure(code:number, responseText:string);

    Attach(irp: IResponseInterpreter) {
        this.SubInterpreters.push(irp);
    }
    Detach(irp: IResponseInterpreter) {
        let index = this.SubInterpreters.indexOf(irp);
        if(index >= 0) {
            this.SubInterpreters.removeAt(index);
        }
    }
}