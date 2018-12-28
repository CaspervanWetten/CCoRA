/// <reference path='../Action/index.ts'/>

abstract class Workspace<W extends Workspace<W>>
{
    protected Action : WorkspaceBoundAction<W> | undefined;

    public Work()
    {
        if(this.Action) {
            this.Action.Invoke();
            this.Action = undefined;
        }
    }

    public SetAction(action : WorkspaceBoundAction<W>)
    {
        this.Action = action;
        this.Work();
    }
}