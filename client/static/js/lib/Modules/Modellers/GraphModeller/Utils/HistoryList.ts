/// <reference path='./index.ts'/>
/// <reference path='../Actions/index.ts'/>

class HistoryList<A extends UndoableAction>
{
    protected Items : A[];
    
    protected Current : number;
    protected Last    : number;
    
    public constructor(actions? : A[])
    {
        this.Current = -1;
        this.Last   = 0;

        this.Items = [];
    }

    public Add(a : A)
    {
        this.Current += 1;
        this.Last = this.Current;
        this.Items[this.Current] = a;
    }

    public Undo()
    {
        if(this.Current >= 0) {
            let a = this.Items[this.Current];
            a.Undo();
        }
        this.Current = Math.max(-1, this.Current - 1);
    }

    public Redo()
    {
        if(this.Current < this.Last) {
            this.Current++;
            let a = this.Items[this.Current];
            a.Invoke();
        }
    }

    public IsEmpty()
    {
        return this.Current < 0;
    }
}