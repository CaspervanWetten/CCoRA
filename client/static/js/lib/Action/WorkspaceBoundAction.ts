/// <reference path='./index.ts'/>

abstract class WorkspaceBoundAction<W extends Workspace<W>> implements Action
{
    protected Workspace : W;
    public constructor(ws : W)
    {
        this.Workspace = ws;
    }

    public abstract Invoke();
}