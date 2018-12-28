/// <reference path='./index.ts'/>

interface IObservable
{
    Attach(observer : IObserver<IObservable>);
    Detach(observer : IObserver<IObservable>);
    Notify();
}