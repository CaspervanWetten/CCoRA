/// <reference path='./index.ts'/>

/// <reference path='../../../../Action/index.ts'/>

abstract class GraphModellerAction implements UndoableAction
{
    public static ElementCounter = 0;

    protected Drawer : GraphDrawer;
    public constructor(m : GraphDrawer)
    {
        this.Drawer = m;
    }

    public abstract Invoke();

    public abstract Undo()
}