/// <reference path='./index.ts'/>

interface UndoableAction extends Action
{
    Undo();
}